<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'ReservaLab') ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <link
        href="/assets/css/app.css"
        rel="stylesheet">
    <link href="/assets/css/dashboard.css" rel="stylesheet">

</head>

<body>

<?php require dirname(__DIR__) . '/partials/navbar.php'; ?>

<div class="container-fluid">

    <div class="row g-0">

        <aside class="col-12 col-md-3 col-lg-2 sidebar">

            <?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>

        </aside>

        <main class="col-12 col-md-9 col-lg-10 content">

            <?php require dirname(__DIR__) . '/partials/flash.php'; ?>

            <?= $content ?>

        </main>

    </div>

</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>