<?php

declare(strict_types=1);

use Core\Session;


$nombre = Session::get('nombre', 'Invitado');
$rol = Session::get('rol', 'Sin rol');
$avatar = (string) Session::get('avatar', '');

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

    <div class="container-fluid">

        <div class="d-flex align-items-center">

            <button
                class="btn btn-primary border-0 shadow-none d-lg-none me-2 px-2"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#sidebarOffcanvas"
                aria-controls="sidebarOffcanvas"
                aria-label="Abrir menú">

                <i class="bi bi-list fs-3"></i>

            </button>

            <a class="navbar-brand fw-bold d-none d-lg-flex align-items-center mb-0" href="/dashboard">

                <i class="bi bi-pc-display-horizontal fs-4 me-2"></i>

                ReservaLab

            </a>

        </div>

        <div class="dropdown">

            <button
                class="btn btn-primary border-0 shadow-none dropdown-toggle d-flex align-items-center"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">

                <?php if ($avatar !== ''): ?>

                    <span class="navbar-avatar me-2"><?= $avatar ?></span>

                <?php else: ?>

                    <i class="bi bi-person-circle fs-3 me-2"></i>

                <?php endif; ?>

                <div class="text-start">

                    <div class="fw-semibold">

                        <?= htmlspecialchars($nombre) ?>

                    </div>

                    <small class="text-white-50">

                        <?= htmlspecialchars(strval($rol)) ?>

                    </small>

                </div>

            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow">

                <li>

                    <a class="dropdown-item" href="/perfil">

                        <i class="bi bi-person me-2"></i>

                        Mi perfil

                    </a>

                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>

                    <a class="dropdown-item text-danger" href="/logout">

                        <i class="bi bi-box-arrow-right me-2"></i>

                        Cerrar sesión

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>