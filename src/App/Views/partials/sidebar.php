<?php

declare(strict_types=1);

use Core\Session;

$current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$esAdmin = (int) Session::get('id_rol', 0) === 1;

function active(string $route, string $current): string
{
    return $current === $route ? 'active' : '';
}

?>

<nav class="sidebar-menu">

    <div class="sidebar-title">
        MENÚ PRINCIPAL
    </div>

    <ul class="nav flex-column">

        <!-- Dashboard -->
        <li class="nav-item">

            <a
                id="tour-nav-dashboard"
                class="nav-link <?= active('/dashboard', $current) ?>"
                href="/dashboard">

                <i class="bi bi-speedometer2"></i>

                Dashboard

            </a>

        </li>

        <?php if ($esAdmin): ?>

        <!-- Usuarios -->
        <li class="nav-item">

            <a
                class="nav-link <?= active('/usuarios', $current) ?>"
                href="/usuarios">

                <i class="bi bi-people-fill"></i>

                Usuarios

            </a>

        </li>

        <!-- Cursos -->
        <li class="nav-item">

            <a
                class="nav-link <?= active('/cursos', $current) ?>"
                href="/cursos">

                <i class="bi bi-mortarboard-fill"></i>

                Cursos

            </a>

        </li>

        <!-- Horarios -->
        <li class="nav-item">

            <a
                class="nav-link <?= active('/horarios', $current) ?>"
                href="/horarios">

                <i class="bi bi-clock-history"></i>

                Horarios

            </a>

        </li>

        <?php endif; ?>

        <!-- Laboratorios -->
        <li class="nav-item">

            <a
                id="tour-nav-laboratorios"
                class="nav-link <?= active('/laboratorios', $current) ?>"
                href="/laboratorios">

                <i class="bi bi-pc-display"></i>

                Laboratorios

            </a>

        </li>

        <!-- Reservas -->
        <li class="nav-item">

            <a
                id="tour-nav-reservas"
                class="nav-link <?= active('/reservas', $current) ?>"
                href="/reservas">

                <i class="bi bi-calendar-check-fill"></i>

                Reservas

            </a>

        </li>

        <?php if ($esAdmin): ?>

        <!-- Observaciones -->
        <li class="nav-item">

            <a
                class="nav-link <?= active('/observaciones', $current) ?>"
                href="/observaciones">

                <i class="bi bi-journal-text"></i>

                Observaciones

            </a>

        </li>

        <!-- Reportes -->
        <li class="nav-item">

            <a
                class="nav-link <?= active('/reportes', $current) ?>"
                href="/reportes">

                <i class="bi bi-bar-chart-fill"></i>

                Reportes

            </a>

        </li>

        <?php endif; ?>

    </ul>

</nav>