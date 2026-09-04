<?php

declare(strict_types=1);

use Core\Session;

/** @var int $totalReservas */
/** @var int $totalProximas */
/** @var int $totalCanceladas */
/** @var array $semanas */
/** @var \DateTimeImmutable $nombreMes */
/** @var int $anioAnterior */
/** @var int $mesAnterior */
/** @var int $anioSiguiente */
/** @var int $mesSiguiente */

$nombre = Session::get('nombre', 'Docente');

date_default_timezone_set('America/Santiago');

$diasSemana = ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá', 'Do'];

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

function horaDashboardDocente(?string $hora): string
{
    return empty($hora) ? '' : substr($hora, 0, 5);
}

/**
 * Color del evento según el estado de la reserva. El horario solo
 * recibe reservas Pendientes o Confirmadas (el controlador ya
 * descarta el resto).
 */
function claseEventoHorarioDocente(array $reserva): string
{
    $estado = mb_strtolower((string) $reserva['estado']);

    return $estado === 'pendiente'
        ? 'evento-pendiente'
        : 'evento-confirmada';
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

    <!-- Mi horario -->
    <div class="card dashboard-card">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <i class="bi bi-calendar3 me-1"></i>
                Mi Horario &mdash;
                <span class="text-capitalize">
                    <?= htmlspecialchars($meses[(int) $nombreMes->format('n')]) ?>
                    <?= htmlspecialchars($nombreMes->format('Y')) ?>
                </span>
            </div>

            <div class="btn-group">

                <a
                    href="/dashboard?anio=<?= $anioAnterior ?>&mes=<?= $mesAnterior ?>"
                    class="btn btn-outline-secondary btn-sm"
                    title="Mes anterior">
                    <i class="bi bi-chevron-left"></i>
                </a>

                <a href="/dashboard" class="btn btn-outline-secondary btn-sm">
                    Hoy
                </a>

                <a
                    href="/dashboard?anio=<?= $anioSiguiente ?>&mes=<?= $mesSiguiente ?>"
                    class="btn btn-outline-secondary btn-sm"
                    title="Mes siguiente">
                    <i class="bi bi-chevron-right"></i>
                </a>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="calendario-scroll">

                <div class="calendario-grid">

                    <?php foreach ($diasSemana as $nombreDia): ?>

                        <div class="calendario-cabecera">
                            <?= htmlspecialchars($nombreDia) ?>
                        </div>

                    <?php endforeach; ?>

                    <?php foreach ($semanas as $semana): ?>

                        <?php foreach ($semana as $dia): ?>

                            <div class="calendario-dia
                                <?= $dia['esMesActual'] ? '' : 'calendario-dia-fuera' ?>
                                <?= $dia['esHoy'] ? 'calendario-dia-hoy' : '' ?>">

                                <div class="calendario-numero">
                                    <?= (int) $dia['dia'] ?>
                                </div>

                                <?php foreach ($dia['reservas'] as $reserva): ?>

                                    <?php

                                    $tituloEvento = horaDashboardDocente($reserva['hora_inicio'] ?? null)
                                        . ' - ' . (string) $reserva['curso']
                                        . ' - ' . (string) $reserva['laboratorio']
                                        . ' (' . (string) $reserva['estado'] . ')';

                                    ?>

                                    <a
                                        href="/reservas/<?= (int) $reserva['id_reserva'] ?>/observaciones"
                                        class="calendario-evento <?= claseEventoHorarioDocente($reserva) ?>"
                                        title="<?= htmlspecialchars($tituloEvento) ?>">

                                        <?= htmlspecialchars(horaDashboardDocente($reserva['hora_inicio'] ?? null)) ?>
                                        &middot;
                                        <?= htmlspecialchars((string) $reserva['curso']) ?>

                                    </a>

                                <?php endforeach; ?>

                            </div>

                        <?php endforeach; ?>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </div>

    <div class="d-flex flex-wrap gap-3 mt-3">

        <span class="calendario-leyenda">
            <span class="calendario-punto evento-confirmada"></span>
            Confirmada
        </span>

        <span class="calendario-leyenda">
            <span class="calendario-punto evento-pendiente"></span>
            Pendiente
        </span>

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
