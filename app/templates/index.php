<!doctype html>
<html lang="vi">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#f7f8fc" />
        <title>TaskFlow — Quản lý công việc</title>
        <link rel="icon" type="image/svg+xml" href="/static/favicon.svg" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap"
            rel="stylesheet"
        />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <link href="/static/styles.css?v=taskflow-layout-10" rel="stylesheet" />
    </head>
        <?php require __DIR__ . '/partials/auth.php'; ?>
        <?php require __DIR__ . '/partials/workspace.php'; ?>
        <?php require __DIR__ . '/partials/dialogs.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/static/app.js" defer></script>
    </body>
</html>