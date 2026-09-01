<?php

declare(strict_types=1);

use Core\Session;

/** @var array $historial */
/** @var int $totalReservas */
/** @var int $totalProximas */
/** @var int $totalCanceladas */

$nombre = Session::get('nombre', 'Docente');

date_default_timezone_set('America/Santiago');

$dias = [
    'Sunday'    => 'Domingo',
    'Monday'    => 'Lunes',
    'Tuesday'   => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday'  => 'Jueves',
    'Friday'    => 'Viernes',
    'Saturday'  => 'Sábado'
];

$meses = [
    1  => 'enero', 2  => 'febrero', 3  => 'marzo', 4  => 'abril',
    5  => 'mayo', 6  => 'junio', 7  => 'julio', 8  => 'agosto',
    9  => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];

$fechaActual = sprintf(
    '%s %d de %s de %d',
    $dias[date('l')],
    (int) date('d'),
    $meses[(int) date('n')],
    (int) date('Y')
);

function fechaDashboardDocente(string $fecha): string
{
    $timestamp = strtotime($fecha);

    return $timestamp === false ? $fecha : date('d/m/Y', $timestamp);
}

function horaDashboardDocente(?string $hora): string
{
    return empty($hora) ? '' : substr($hora, 0, 5);
}

?>

<div class="container-fluid">

    <!-- Bienvenida -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-12 col-md-8">

                    <h2 class="fw-bold mb-2">
                        👋 Bienvenido, <?= htmlspecialchars((string) $nombre) ?>
                    </h2>

                </div>

                <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">

                    <h6 class="text-secondary mb-1">
                        <i class="bi bi-calendar-event me-1"></i>
                        <?= htmlspecialchars($fechaActual) ?>
                    </h6>

                    <h4 class="text-primary mb-0">
                        <i class="bi bi-clock me-1"></i>
                        <?= date('H:i') ?>
                    </h4>

                </div>

            </div>

        </div>

    </div>

    <!-- Estadísticas -->
    <div id="tour-stats" class="row g-4 mb-4">

        <div class="col-12 col-sm-4">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="stat-label">Mis Reservas</div>
                            <div class="stat-number"><?= $totalReservas ?></div>
                            <small class="text-secondary">Este mes</small>
                        </div>

                        <div class="stat-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-4">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="stat-label">Próximas</div>
                            <div class="stat-number"><?= $totalProximas ?></div>
                            <small class="text-success">Vigentes</small>
                        </div>

                        <div class="stat-icon">
                            <i class="bi bi-calendar-week"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-4">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="stat-label">Canceladas</div>
                            <div class="stat-number"><?= $totalCanceladas ?></div>
                            <small class="text-secondary">Historial</small>
                        </div>

                        <div class="stat-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Acceso rápido -->
    <div class="d-flex justify-content-end mb-3">

        <a id="tour-nueva-reserva" href="/reservas" class="btn btn-success">
            <i class="bi bi-calendar-plus me-1"></i>
            Nueva Reserva
        </a>

    </div>

    <!-- Historial -->
    <div class="card dashboard-card">

        <div class="card-header">
            <i class="bi bi-clock-history me-1"></i>
            Historial de Reservas (este mes)
        </div>

        <div class="card-body p-0">

            <?php if (empty($historial)): ?>

                <div class="empty-state py-5">
                    <i class="bi bi-calendar-x"></i>
                    <h5>Aún no tiene reservas este mes</h5>
                    <p class="mb-3">Cuando realice una reserva aparecerá aquí.</p>
                    <a href="/reservas" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-calendar-plus me-1"></i>
                        Crear reserva
                    </a>
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Bloque</th>
                                <th>Laboratorio</th>
                                <th>Curso</th>
                                <th>Estado</th>
                                <th class="text-center">Observaciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($historial as $reserva): ?>

                                <?php

                                $estado = strtolower((string) ($reserva['estado'] ?? ''));

                                $badgeEstado = match ($estado) {
                                    'pendiente'  => 'bg-warning text-dark',
                                    'confirmada' => 'bg-success',
                                    'finalizada' => 'bg-secondary',
                                    'cancelada'  => 'bg-danger',
                                    default      => 'bg-secondary'
                                };

                                ?>

                                <tr>

                                    <td><?= htmlspecialchars(fechaDashboardDocente((string) $reserva['fecha'])) ?></td>

                                    <td>
                                        <?= htmlspecialchars((string) $reserva['horario']) ?>
                                        <small class="text-muted d-block">
                                            <?= horaDashboardDocente($reserva['hora_inicio'] ?? null) ?>
                                            -
                                            <?= horaDashboardDocente($reserva['hora_fin'] ?? null) ?>
                                        </small>
                                    </td>

                                    <td><?= htmlspecialchars((string) $reserva['laboratorio']) ?></td>

                                    <td><?= htmlspecialchars((string) $reserva['curso']) ?></td>

                                    <td>
                                        <span class="badge <?= $badgeEstado ?>">
                                            <?= htmlspecialchars((string) $reserva['estado']) ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a
                                            href="/reservas/<?= (int) $reserva['id_reserva'] ?>/observaciones"
                                            class="btn btn-sm btn-outline-dark"
                                            title="Ver observaciones">
                                            <i class="bi bi-journal-text"></i>
                                        </a>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php if ($mostrarTutorial ?? false): ?>

    <!-- Tour guiado de bienvenida (solo Docente, solo la primera vez) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css">
    <link rel="stylesheet" href="<?= \Core\Asset::url('/assets/css/tour.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>

    <script>
        window.tourConfig = {
            modulo: 'dashboard',
            mostrar: true,
            token: '<?= addslashes(\Core\Csrf::token()) ?>',
            steps: [
                {
                    popover: {
                        title: '👋 ¡Bienvenido a ReservaLab!',
                        description: 'Te mostramos rápidamente cómo funciona el sistema antes de empezar. Puedes cerrar este recorrido en cualquier momento.'
                    }
                },
                {
                    element: '#tour-nav-dashboard',
                    popover: {
                        title: 'Dashboard',
                        description: 'Aquí siempre puedes volver a tu panel principal, con el resumen de tus reservas del mes.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-nav-laboratorios',
                    popover: {
                        title: 'Laboratorios',
                        description: 'Aquí ves los laboratorios disponibles, su capacidad, y puedes reservarlos directamente desde el botón "Reservar".',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-nav-reservas',
                    popover: {
                        title: 'Reservas',
                        description: 'Aquí puedes crear una nueva reserva: eliges fecha, laboratorio, curso y hasta 3 bloques horarios.',
                        side: 'right',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-nueva-reserva',
                    popover: {
                        title: 'Acceso rápido',
                        description: 'También puedes crear una reserva directamente desde este botón, sin pasar por el menú.',
                        side: 'bottom',
                        align: 'end'
                    }
                },
                {
                    element: '#tour-stats',
                    popover: {
                        title: 'Tu resumen',
                        description: 'Aquí ves cuántas reservas tienes este mes, cuáles están próximas y cuáles fueron canceladas.',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#tour-navbar-perfil',
                    popover: {
                        title: 'Tu perfil',
                        description: 'Desde aquí puedes cambiar tu avatar, tu contraseña, o cerrar sesión.',
                        side: 'bottom',
                        align: 'end'
                    }
                },
                {
                    popover: {
                        title: '✅ ¡Listo!',
                        description: 'Ya puedes empezar a usar ReservaLab. Recuerda que confirmar o cancelar tus reservas se hace desde el enlace que te llega por correo.'
                    }
                }
            ]
        };
    </script>

    <script src="<?= \Core\Asset::url('/assets/js/tutorial.js') ?>"></script>

<?php endif; ?>
