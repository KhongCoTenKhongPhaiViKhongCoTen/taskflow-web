<?php

declare(strict_types=1);

const ROOT_DIR = __DIR__ . "/../../";

function loadEnvironment(): void
{
    $file = ROOT_DIR . ".env";
    if (!is_file($file)) {
        return;
    }
    foreach (
        file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []
        as $line
    ) {
        $line = trim($line);
        if (
            $line === "" ||
            str_starts_with($line, "#") ||
            !str_contains($line, "=")
        ) {
            continue;
        }
        [$key, $value] = explode("=", $line, 2);
        $key = trim($key);
        $value = trim(trim($value), "\"'");
        if ($key !== "" && getenv($key) === false) {
            putenv($key . "=" . $value);
            $_ENV[$key] = $value;
        }
    }
}

function envValue(string $key, string $default = ""): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function jsonResponse(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header("Content-Type: application/json; charset=utf-8");
    header("Cache-Control: no-store");
    if ($status !== 204) {
        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_INVALID_UTF8_SUBSTITUTE,
        );
    }
    exit();
}

function fail(int $status, string $message): never
{
    jsonResponse(["detail" => $message], $status);
}

function readJson(): array
{
    $raw = file_get_contents("php://input");
    $data = json_decode($raw === false ? "" : $raw, true);
    if (!is_array($data)) {
        fail(422, "Request body must be a JSON object");
    }
    return $data;
}

function requiredString(array $data, string $field, int $min, int $max): string
{
    $value = $data[$field] ?? null;
    if (
        !is_string($value) ||
        mb_strlen($value) < $min ||
        mb_strlen($value) > $max
    ) {
        fail(422, "Invalid field: {$field}");
    }
    return $value;
}

function taskPayload(array $data): array
{
    $title = $data["title"] ?? null;
    if (!is_string($title)) {
        fail(422, "Title is required");
    }
    $title = trim($title);
    if ($title === "" || mb_strlen($title) > 160) {
        fail(422, "Title must contain 1 to 160 characters");
    }

    $description = $data["description"] ?? "";
    $status = $data["status"] ?? "todo";
    $priority = $data["priority"] ?? "medium";
    $dueDate = $data["due_date"] ?? null;
    $myDayDate = $data["my_day_date"] ?? null;
    $isImportant = $data["is_important"] ?? false;
    if (!is_string($description) || mb_strlen($description) > 10000) {
        fail(422, "Invalid description");
    }
    if (!in_array($status, ["todo", "doing", "done"], true)) {
        fail(422, "Invalid status");
    }
    if (!in_array($priority, ["low", "medium", "high"], true)) {
        fail(422, "Invalid priority");
    }
    foreach (
        ["due_date" => $dueDate, "my_day_date" => $myDayDate]
        as $field => $value
    ) {
        if ($value !== null) {
            if (!is_string($value)) {
                fail(422, "Invalid {$field}");
            }
            $parsed = DateTimeImmutable::createFromFormat("!Y-m-d", $value);
            if ($parsed === false || $parsed->format("Y-m-d") !== $value) {
                fail(422, "{$field} must use YYYY-MM-DD");
            }
        }
    }
    if (!is_bool($isImportant)) {
        fail(422, "is_important must be a boolean");
    }

    return [
        "title" => $title,
        "description" => $description,
        "status" => $status,
        "priority" => $priority,
        "due_date" => $dueDate,
        "my_day_date" => $myDayDate,
        "is_important" => $isImportant,
    ];
}

function publicUser(array $user): array
{
    $public = ["id" => (int) $user["id"], "username" => $user["username"]];
    if (isset($user["email"])) {
        $public["email"] = $user["email"];
    }
    return $public;
}

function currentUser(PDO $db): array
{
    $userId = $_SESSION["user_id"] ?? null;
    if (!is_int($userId) && !ctype_digit((string) $userId)) {
        fail(401, "Authentication required");
    }
    $statement = $db->prepare(
        "SELECT id, username, email FROM users WHERE id = ?",
    );
    $statement->execute([(int) $userId]);
    $user = $statement->fetch();
    if (!$user) {
        $_SESSION = [];
        fail(401, "Authentication required");
    }
    return $user;
}

function taskRow(array $row): array
{
    $row["id"] = (int) $row["id"];
    $row["user_id"] = (int) $row["user_id"];
    $row["is_important"] = (bool) $row["is_important"];
    return $row;
}

loadEnvironment();
$path = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?: "/";
$method = strtoupper($_SERVER["REQUEST_METHOD"] ?? "GET");

if ($path === "/health" && $method === "GET") {
    jsonResponse(["status" => "ok"]);
}
if (!str_starts_with($path, "/api/")) {
    fail(404, "Not found");
}

$secureCookie = filter_var(
    envValue("COOKIE_SECURE", "false"),
    FILTER_VALIDATE_BOOLEAN,
);
session_name("taskflow_php_session");
session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => $secureCookie,
    "httponly" => true,
    "samesite" => "Lax",
]);
session_start();

try {
    $databaseUrl = envValue(
        "DATABASE_URL",
        "mysql+pymysql://taskflow:taskflow_dev_password@127.0.0.1:3306/taskflow?charset=utf8mb4",
    );
    if (
        !preg_match(
            "~^mysql\+pymysql://([^:]+):([^@]*)@([^:/]+)(?::(\d+))?/([^?]+)~",
            $databaseUrl,
            $matches,
        )
    ) {
        throw new RuntimeException(
            "DATABASE_URL must use mysql+pymysql://user:password@host:port/database",
        );
    }
    [, $dbUser, $dbPassword, $dbHost, $dbPort, $dbName] = array_pad(
        $matches,
        6,
        "",
    );
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
        $dbHost,
        $dbPort ?: "3306",
        $dbName,
    );
    $db = new PDO($dsn, rawurldecode($dbUser), rawurldecode($dbPassword), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    if ($path === "/api/register" && $method === "POST") {
        $input = readJson();
        $username = requiredString($input, "username", 3, 40);
        if (preg_match("/\s/u", $username)) {
            fail(422, "Username cannot contain whitespace");
        }
        $email = requiredString($input, "email", 3, 254);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            fail(422, "Invalid email address");
        }
        $password = requiredString($input, "password", 8, 128);
        $statement = $db->prepare(
            "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)",
        );
        try {
            $statement->execute([
                $username,
                mb_strtolower($email),
                password_hash($password, PASSWORD_ARGON2ID),
            ]);
        } catch (PDOException $error) {
            if ($error->getCode() === "23000") {
                fail(409, "Username or email is already in use");
            }
            throw $error;
        }
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int) $db->lastInsertId();
        jsonResponse(
            [
                "id" => $_SESSION["user_id"],
                "username" => $username,
                "email" => mb_strtolower($email),
            ],
            201,
        );
    }

    if ($path === "/api/login" && $method === "POST") {
        $input = readJson();
        $username = requiredString($input, "username", 1, 40);
        $password = requiredString($input, "password", 1, 128);
        $statement = $db->prepare(
            "SELECT id, username, email, password_hash FROM users WHERE username = ?",
        );
        $statement->execute([$username]);
        $user = $statement->fetch();
        if (!$user || !password_verify($password, $user["password_hash"])) {
            fail(401, "Invalid username or password");
        }
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int) $user["id"];
        jsonResponse(publicUser($user));
    }

    if ($path === "/api/logout" && $method === "POST") {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $cookie = session_get_cookie_params();
            setcookie(session_name(), "", [
                "expires" => time() - 42000,
                "path" => $cookie["path"],
                "secure" => $cookie["secure"],
                "httponly" => $cookie["httponly"],
                "samesite" => "Lax",
            ]);
        }
        session_destroy();
        jsonResponse(["ok" => true]);
    }

    if ($path === "/api/me" && $method === "GET") {
        jsonResponse(publicUser(currentUser($db)));
    }

    if ($path === "/api/account" && $method === "PUT") {
        $user = currentUser($db);
        $input = readJson();
        $currentPassword = requiredString($input, "current_password", 1, 128);
        $statement = $db->prepare(
            "SELECT password_hash FROM users WHERE id = ?",
        );
        $statement->execute([(int) $user["id"]]);
        $passwordHash = $statement->fetchColumn();
        if (
            !$passwordHash ||
            !password_verify($currentPassword, $passwordHash)
        ) {
            fail(422, "Mật khẩu hiện tại không chính xác");
        }

        $username = array_key_exists("username", $input)
            ? requiredString($input, "username", 3, 40)
            : $user["username"];
        if (preg_match("/\\s/u", $username)) {
            fail(422, "Username cannot contain whitespace");
        }
        $email = array_key_exists("email", $input)
            ? requiredString($input, "email", 3, 254)
            : $user["email"];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            fail(422, "Invalid email address");
        }
        $newPasswordHash = $passwordHash;
        if (array_key_exists("new_password", $input)) {
            $newPassword = requiredString($input, "new_password", 8, 128);
            $newPasswordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
        }

        try {
            $statement = $db->prepare(
                "UPDATE users SET username = ?, email = ?, password_hash = ? WHERE id = ?",
            );
            $statement->execute([
                $username,
                mb_strtolower($email),
                $newPasswordHash,
                (int) $user["id"],
            ]);
        } catch (PDOException $error) {
            if ($error->getCode() === "23000") {
                fail(409, "Username or email is already in use");
            }
            throw $error;
        }
        jsonResponse([
            "id" => (int) $user["id"],
            "username" => $username,
            "email" => mb_strtolower($email),
        ]);
    }

    if ($path === "/api/tasks" && $method === "GET") {
        $user = currentUser($db);
        $status = $_GET["status"] ?? "all";
        $priority = $_GET["priority"] ?? "all";
        $view = $_GET["view"] ?? "all";
        $query = trim((string) ($_GET["q"] ?? ""));
        if (
            !in_array($status, ["todo", "doing", "done", "all"], true) ||
            !in_array($priority, ["low", "medium", "high", "all"], true) ||
            !in_array($view, ["all", "my_day", "important"], true)
        ) {
            fail(422, "Invalid filter");
        }
        $sql =
            "SELECT id, user_id, title, description, status, priority, due_date, my_day_date, is_important, created_at, updated_at FROM tasks WHERE user_id = ?";
        $parameters = [(int) $user["id"]];
        if ($query !== "") {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $parameters[] = "%" . $query . "%";
            $parameters[] = "%" . $query . "%";
        }
        if ($status !== "all") {
            $sql .= " AND status = ?";
            $parameters[] = $status;
        }
        if ($priority !== "all") {
            $sql .= " AND priority = ?";
            $parameters[] = $priority;
        }
        if ($view === "my_day") {
            $sql .= " AND my_day_date = CURRENT_DATE";
        } elseif ($view === "important") {
            $sql .= " AND is_important = 1";
        }
        $sql .=
            " ORDER BY is_important DESC, due_date IS NULL, due_date ASC, id ASC";
        $statement = $db->prepare($sql);
        $statement->execute($parameters);
        jsonResponse(array_map("taskRow", $statement->fetchAll()));
    }

    if ($path === "/api/tasks" && $method === "POST") {
        $user = currentUser($db);
        $task = taskPayload(readJson());
        $statement = $db->prepare(
            "INSERT INTO tasks (user_id, title, description, status, priority, due_date, my_day_date, is_important) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        );
        $statement->execute([
            (int) $user["id"],
            $task["title"],
            $task["description"],
            $task["status"],
            $task["priority"],
            $task["due_date"],
            $task["my_day_date"],
            (int) $task["is_important"],
        ]);
        $id = (int) $db->lastInsertId();
        $statement = $db->prepare(
            "SELECT id, user_id, title, description, status, priority, due_date, my_day_date, is_important, created_at, updated_at FROM tasks WHERE id = ? AND user_id = ?",
        );
        $statement->execute([$id, (int) $user["id"]]);
        jsonResponse(taskRow($statement->fetch()), 201);
    }

    if (preg_match('~^/api/tasks/(\d+)$~', $path, $idMatch)) {
        $user = currentUser($db);
        $taskId = (int) $idMatch[1];
        if ($method === "PUT") {
            $task = taskPayload(readJson());
            $statement = $db->prepare(
                "UPDATE tasks SET title = ?, description = ?, status = ?, priority = ?, due_date = ?, my_day_date = ?, is_important = ? WHERE id = ? AND user_id = ?",
            );
            $statement->execute([
                $task["title"],
                $task["description"],
                $task["status"],
                $task["priority"],
                $task["due_date"],
                $task["my_day_date"],
                (int) $task["is_important"],
                $taskId,
                (int) $user["id"],
            ]);
            $statement = $db->prepare(
                "SELECT id, user_id, title, description, status, priority, due_date, my_day_date, is_important, created_at, updated_at FROM tasks WHERE id = ? AND user_id = ?",
            );
            $statement->execute([$taskId, (int) $user["id"]]);
            $updated = $statement->fetch();
            if (!$updated) {
                fail(404, "Task not found");
            }
            jsonResponse(taskRow($updated));
        }
        if ($method === "DELETE") {
            $statement = $db->prepare(
                "DELETE FROM tasks WHERE id = ? AND user_id = ?",
            );
            $statement->execute([$taskId, (int) $user["id"]]);
            if ($statement->rowCount() === 0) {
                fail(404, "Task not found");
            }
            jsonResponse(null, 204);
        }
    }

    if ($path === "/api/dashboard" && $method === "GET") {
        $user = currentUser($db);
        $view = $_GET["view"] ?? "all";
        if (!in_array($view, ["all", "my_day", "important"], true)) {
            fail(422, "Invalid dashboard view");
        }
        $viewCondition = match ($view) {
            "my_day" => " AND my_day_date = CURRENT_DATE",
            "important" => " AND is_important = 1",
            default => "",
        };
        $statement = $db->prepare(
            "SELECT status, COUNT(*) AS total FROM tasks WHERE user_id = ?{$viewCondition} GROUP BY status",
        );
        $statement->execute([(int) $user["id"]]);
        $counts = ["todo" => 0, "doing" => 0, "done" => 0];
        foreach ($statement->fetchAll() as $row) {
            $counts[$row["status"]] = (int) $row["total"];
        }
        $statement = $db->prepare(
            "SELECT COUNT(*) FROM tasks WHERE user_id = ?{$viewCondition} AND due_date < CURRENT_DATE AND status <> 'done'",
        );
        $statement->execute([(int) $user["id"]]);
        jsonResponse([
            "total" => array_sum($counts),
            ...$counts,
            "overdue" => (int) $statement->fetchColumn(),
        ]);
    }

    fail(404, "Not found");
} catch (Throwable $error) {
    error_log((string) $error);
    fail(500, "Internal server error");
}
