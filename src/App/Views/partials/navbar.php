<?php

declare(strict_types=1);

use Core\Session;


$nombre = Session::get('nombre', 'Invitado');
$rol = Session::get('rol', 'Sin rol');

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold d-flex align-items-center" href="/dashboard">

            <i class="bi bi-pc-display-horizontal fs-4 me-2"></i>

            ReservaLab

        </a>

        <div class="dropdown">

            <button
                class="btn btn-primary border-0 shadow-none dropdown-toggle d-flex align-items-center"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">

                <i class="bi bi-person-circle fs-3 me-2"></i>

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