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
        href="<?= \Core\Asset::url('/assets/css/brand.css') ?>"
        rel="stylesheet">
    <link
        href="<?= \Core\Asset::url('/assets/css/app.css') ?>"
        rel="stylesheet">
    <link href="<?= \Core\Asset::url('/assets/css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= \Core\Asset::url('/assets/css/avatar.css') ?>" rel="stylesheet">

</head>

<body>

<?php require dirname(__DIR__) . '/partials/navbar.php'; ?>

<div class="container-fluid">

    <div class="row g-0">

        <aside
            class="offcanvas-lg offcanvas-start col-lg-2 sidebar"
            tabindex="-1"
            id="sidebarOffcanvas"
            aria-labelledby="sidebarOffcanvasLabel">

            <div class="offcanvas-header d-lg-none border-bottom">

                <h5 class="offcanvas-title fw-bold d-flex align-items-center" id="sidebarOffcanvasLabel">

                    <img
                        src="<?= \Core\Asset::url('/assets/img/logo-huerton.png') ?>"
                        alt="Liceo El Huerton"
                        class="navbar-logo me-2">

                    ReservaLab

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas"
                    data-bs-target="#sidebarOffcanvas"
                    aria-label="Cerrar">
                </button>

            </div>

            <div class="offcanvas-body d-block">

                <?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>

            </div>

        </aside>

        <main class="col-12 col-lg-10 content">

            <?php require dirname(__DIR__) . '/partials/flash.php'; ?>

            <?= $content ?>

        </main>

    </div>

</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>