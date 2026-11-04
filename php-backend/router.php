<?php

declare(strict_types=1);

$uriPath = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?: "/";
$projectRoot = dirname(__DIR__);

if (str_starts_with($uriPath, "/static/")) {
    $relativePath = substr($uriPath, strlen("/static/"));
    $staticRoot = realpath($projectRoot . "/app/static");
    $filePath = realpath($projectRoot . "/app/static/" . $relativePath);
    if (
        $staticRoot !== false &&
        $filePath !== false &&
        str_starts_with($filePath, $staticRoot . DIRECTORY_SEPARATOR) &&
        is_file($filePath)
    ) {
        $mimeTypes = [
            "css" => "text/css; charset=utf-8",
            "js" => "text/javascript; charset=utf-8",
            "svg" => "image/svg+xml",
        ];
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        header(
            "Content-Type: " .
                ($mimeTypes[$extension] ?? "application/octet-stream"),
        );
        readfile($filePath);
        return true;
    }
    http_response_code(404);
    return true;
}

if (
    $uriPath === "/" &&
    strtoupper($_SERVER["REQUEST_METHOD"] ?? "GET") === "GET"
) {
    require $projectRoot . "/app/templates/index.php";
    return true;
}

require __DIR__ . "/public/index.php";
