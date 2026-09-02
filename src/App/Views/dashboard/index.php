<?php

declare(strict_types=1);

use Core\Session;

$nombre = Session::get('nombre', 'Administrador');

date_default_timezone_set('America/Santiago');

/*
|--------------------------------------------------------------------------
| Valores recibidos desde DashboardController
|--------------------------------------------------------------------------
*/

$totalUsuarios = $totalUsuarios ?? 0;
$totalLaboratorios = $totalLaboratorios ?? 0;
$totalReservas = $totalReservas ?? 0;
$totalCursos = $totalCursos ?? 0;

$proximasReservas = $proximasReservas ?? [];
$laboratoriosDashboard = $laboratoriosDashboard ?? [];

/*
|--------------------------------------------------------------------------
| Fecha en español
|--------------------------------------------------------------------------
*/

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
    1  => 'enero',
    2  => 'febrero',
    3  => 'marzo',
    4  => 'abril',
    5  => 'mayo',
    6  => 'junio',
    7  => 'julio',
    8  => 'agosto',
    9  => 'septiembre',
    10 => 'octubre',
    11 => 'noviembre',
    12 => 'diciembre'
];

$fechaActual = sprintf(
    '%s %d de %s de %d',
    $dias[date('l')],
    (int) date('d'),
    $meses[(int) date('n')],
    (int) date('Y')
);

/*
|--------------------------------------------------------------------------
| Funciones auxiliares
|--------------------------------------------------------------------------
*/

function fechaDashboard(string $fecha): string
{
    $timestamp = strtotime($fecha);

    if ($timestamp === false) {
        return $fecha;
    }

    return date('d/m/Y', $timestamp);
}

function horaDashboard(?string $hora): string
{
    if (empty($hora)) {
        return '';
    }

    return substr($hora, 0, 5);
}

?>

<div class="container-fluid">

    <!-- ============================================================= -->
    <!-- BIENVENIDA -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-12 col-md-8">

                    <h2 class="fw-bold mb-2">

                        👋 Bienvenido,
                        <?= htmlspecialchars((string) $nombre) ?>

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


    <!-- ============================================================= -->
    <!-- TARJETAS DE ESTADÍSTICAS -->
    <!-- ============================================================= -->

    <div class="row g-4 mb-4">

        <!-- Usuarios -->

        <div class="col-12 col-sm-6 col-lg-3">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="stat-label">
                                Usuarios
                            </div>

                            <div class="stat-number">

                                <?= (int) $totalUsuarios ?>

                            </div>

                            <small class="text-success">
                                Registrados
                            </small>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-people-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Laboratorios -->

        <div class="col-12 col-sm-6 col-lg-3">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="stat-label">
                                Laboratorios
                            </div>

                            <div class="stat-number">

                                <?= (int) $totalLaboratorios ?>

                            </div>

                            <small class="text-success">
                                Activos
                            </small>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-pc-display"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Reservas Hoy -->

        <div class="col-12 col-sm-6 col-lg-3">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="stat-label">
                                Reservas Hoy
                            </div>

                            <div class="stat-number">

                                <?= (int) $totalReservas ?>

                            </div>

                            <small class="text-secondary">
                                Confirmadas
                            </small>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-calendar-check-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Cursos -->

        <div class="col-12 col-sm-6 col-lg-3">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="stat-label">
                                Cursos
                            </div>

                            <div class="stat-number">

                                <?= (int) $totalCursos ?>

                            </div>

                            <small class="text-secondary">
                                Creados
                            </small>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-mortarboard-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PRÓXIMAS RESERVAS + ACCESOS RÁPIDOS -->
    <!-- ============================================================= -->

    <div class="row">

        <!-- Próximas Reservas -->

        <div class="col-12 col-lg-8 mb-4">

            <div class="card dashboard-card h-100">

                <div
                    class="card-header d-flex justify-content-between align-items-center">

                    <div>

                        <i class="bi bi-calendar-week me-1"></i>

                        Próximas Reservas

                    </div>

                    <?php if (!empty($proximasReservas)): ?>

                        <span class="badge bg-primary">

                            <?= count($proximasReservas) ?>

                        </span>

                    <?php endif; ?>

                </div>


                <div class="card-body p-0">

                    <?php if (empty($proximasReservas)): ?>

                        <!-- Sin reservas -->

                        <div class="empty-state py-5">

                            <i class="bi bi-calendar-x"></i>

                            <h5>
                                No hay reservas programadas
                            </h5>

                            <p class="mb-3">
                                Cuando existan reservas aparecerán aquí.
                            </p>

                            <a
                                href="/reservas"
                                class="btn btn-outline-primary btn-sm">

                                <i class="bi bi-calendar-plus me-1"></i>

                                Crear reserva

                            </a>

                        </div>

                    <?php else: ?>

                        <!-- Reservas reales -->

                        <div class="list-group list-group-flush">

                            <?php foreach ($proximasReservas as $reserva): ?>

                                <?php

                                $estado = strtolower(
                                    (string) ($reserva['estado'] ?? '')
                                );

                                $badgeEstado = match ($estado) {
                                    'pendiente'  => 'bg-warning text-dark',
                                    'confirmada' => 'bg-success',
                                    'finalizada' => 'bg-secondary',
                                    'cancelada'  => 'bg-danger',
                                    default      => 'bg-secondary'
                                };

                                $idLaboratorio = (int) (
                                    $reserva['id_laboratorio'] ?? 0
                                );

                                $fechaReserva = (string) (
                                    $reserva['fecha'] ?? ''
                                );

                                $urlAgenda = '/reservas?' . http_build_query([
                                    'fecha' => $fechaReserva,
                                    'id_laboratorio' => $idLaboratorio
                                ]);

                                ?>

                                <div class="list-group-item px-4 py-3">

                                    <div class="row align-items-center g-3">

                                        <!-- Fecha -->

                                        <div class="col-6 col-md-2">

                                            <div class="text-primary fw-bold">

                                                <i class="bi bi-calendar-event me-1"></i>

                                                <?= htmlspecialchars(
                                                    fechaDashboard($fechaReserva)
                                                ) ?>

                                            </div>

                                        </div>


                                        <!-- Bloque -->

                                        <div class="col-6 col-md-3">

                                            <div class="fw-bold">

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $reserva['horario']
                                                        ?? 'Bloque'
                                                    )
                                                ) ?>

                                            </div>

                                            <small class="text-muted">

                                                <i class="bi bi-clock me-1"></i>

                                                <?= htmlspecialchars(
                                                    horaDashboard(
                                                        $reserva['hora_inicio']
                                                        ?? null
                                                    )
                                                ) ?>

                                                -

                                                <?= htmlspecialchars(
                                                    horaDashboard(
                                                        $reserva['hora_fin']
                                                        ?? null
                                                    )
                                                ) ?>

                                            </small>

                                        </div>


                                        <!-- Laboratorio / Curso -->

                                        <div class="col-6 col-md-3">

                                            <div class="fw-semibold">

                                                <i class="bi bi-pc-display me-1"></i>

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $reserva['laboratorio']
                                                        ?? ''
                                                    )
                                                ) ?>

                                            </div>

                                            <small class="text-muted">

                                                <i class="bi bi-mortarboard me-1"></i>

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $reserva['curso']
                                                        ?? ''
                                                    )
                                                ) ?>

                                            </small>

                                        </div>


                                        <!-- Responsable -->

                                        <div class="col-6 col-md-2">

                                            <small class="text-muted d-block">
                                                Responsable
                                            </small>

                                            <span>

                                                <?= htmlspecialchars(
                                                    trim(
                                                        (string) (
                                                            $reserva['nombres']
                                                            ?? ''
                                                        )
                                                        . ' '
                                                        . (string) (
                                                            $reserva['apellidos']
                                                            ?? ''
                                                        )
                                                    )
                                                ) ?>

                                            </span>

                                        </div>


                                        <!-- Estado / acción -->

                                        <div class="col-12 col-md-2 text-md-end mt-2 mt-md-0">

                                            <span
                                                class="badge <?= $badgeEstado ?> mb-2">

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $reserva['estado']
                                                        ?? ''
                                                    )
                                                ) ?>

                                            </span>

                                            <div>

                                                <a
                                                    href="<?= htmlspecialchars($urlAgenda) ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Ver en agenda">

                                                    <i class="bi bi-eye"></i>

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>


                        <div class="p-3 border-top text-end">

                            <a
                                href="/reservas/calendario"
                                class="btn btn-sm btn-outline-primary">

                                Ver agenda de reservas

                                <i class="bi bi-arrow-right ms-1"></i>

                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <!-- Accesos rápidos -->

        <div class="col-12 col-lg-4 mb-4">

            <div class="card dashboard-card h-100">

                <div class="card-header">

                    <i class="bi bi-lightning-charge-fill me-1"></i>

                    Accesos rápidos

                </div>

                <div class="card-body d-grid gap-2">

                    <a
                        href="/usuarios/crear"
                        class="btn btn-primary quick-action">

                        <i class="bi bi-person-plus-fill me-1"></i>

                        Nuevo Usuario

                    </a>


                    <a
                        href="/reservas"
                        class="btn btn-outline-success quick-action">

                        <i class="bi bi-calendar-plus me-1"></i>

                        Nueva Reserva

                    </a>


                    <!--
                        Todavía no tenemos módulo de administración
                        de laboratorios terminado.
                    -->

                    <button
                        type="button"
                        class="btn btn-outline-secondary quick-action"
                        disabled>

                        <i class="bi bi-pc-display me-1"></i>

                        Laboratorios

                    </button>


                    <!--
                        Reportes todavía no implementado.
                    -->

                    <button
                        type="button"
                        class="btn btn-outline-dark quick-action"
                        disabled>

                        <i class="bi bi-bar-chart-fill me-1"></i>

                        Reportes

                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- LABORATORIOS -->
    <!-- ============================================================= -->

    <div class="row">

        <?php if (empty($laboratoriosDashboard)): ?>

            <div class="col-12 mb-4">

                <div class="card dashboard-card">

                    <div class="card-body">

                        <div class="alert alert-secondary mb-0">

                            <i class="bi bi-info-circle me-2"></i>

                            No existen laboratorios activos.

                        </div>

                    </div>

                </div>

            </div>

        <?php else: ?>

            <?php foreach ($laboratoriosDashboard as $item): ?>

                <?php

                $laboratorio = $item['laboratorio'];

                $proximaReserva =
                    $item['proxima_reserva'] ?? null;

                $idLaboratorio =
                    (int) $laboratorio->id_laboratorio;

                /*
                 * Si tiene próxima reserva, el botón abre
                 * directamente esa fecha.
                 *
                 * Si no tiene, abre la agenda de hoy.
                 */
                $fechaAgenda = $proximaReserva
                    ? (string) $proximaReserva['fecha']
                    : date('Y-m-d');

                $urlAgendaLaboratorio =
                    '/reservas?' . http_build_query([
                        'fecha' => $fechaAgenda,
                        'id_laboratorio' => $idLaboratorio
                    ]);

                ?>

                <div class="col-12 col-lg-6 mb-4">

                    <div class="card dashboard-card h-100">

                        <div class="card-body">

                            <div
                                class="d-flex justify-content-between align-items-start mb-3">

                                <div>

                                    <h5 class="fw-bold mb-1">

                                        <i class="bi bi-pc-display me-2"></i>

                                        <?= htmlspecialchars(
                                            $laboratorio->nombre
                                        ) ?>

                                    </h5>

                                    <div class="text-muted">

                                        <i class="bi bi-geo-alt me-1"></i>

                                        <?= htmlspecialchars(
                                            $laboratorio->ubicacion
                                        ) ?>

                                    </div>

                                </div>

                                <span class="badge bg-success">

                                    Activo

                                </span>

                            </div>


                            <div class="row g-3 mb-3">

                                <!-- Capacidad -->

                                <div class="col-6">

                                    <div
                                        class="border rounded p-3 h-100">

                                        <small
                                            class="text-muted d-block mb-1">

                                            Capacidad

                                        </small>

                                        <strong>

                                            <i class="bi bi-people me-1"></i>

                                            <?= (int) $laboratorio->capacidad ?>

                                            computadores

                                        </strong>

                                    </div>

                                </div>


                                <!-- Próxima reserva -->

                                <div class="col-6">

                                    <div
                                        class="border rounded p-3 h-100">

                                        <small
                                            class="text-muted d-block mb-1">

                                            Próxima reserva

                                        </small>

                                        <?php if ($proximaReserva): ?>

                                            <strong class="d-block">

                                                <?= htmlspecialchars(
                                                    fechaDashboard(
                                                        (string)
                                                        $proximaReserva['fecha']
                                                    )
                                                ) ?>

                                            </strong>

                                            <small class="text-muted">

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $proximaReserva['horario']
                                                        ?? ''
                                                    )
                                                ) ?>

                                                ·

                                                <?= htmlspecialchars(
                                                    horaDashboard(
                                                        $proximaReserva['hora_inicio']
                                                        ?? null
                                                    )
                                                ) ?>

                                                -

                                                <?= htmlspecialchars(
                                                    horaDashboard(
                                                        $proximaReserva['hora_fin']
                                                        ?? null
                                                    )
                                                ) ?>

                                            </small>

                                        <?php else: ?>

                                            <span class="text-success">

                                                <i class="bi bi-check-circle me-1"></i>

                                                Sin reservas próximas

                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>


                            <?php if ($proximaReserva): ?>

                                <div class="mb-3">

                                    <span class="badge bg-light text-dark border">

                                        <i class="bi bi-mortarboard me-1"></i>

                                        <?= htmlspecialchars(
                                            (string) (
                                                $proximaReserva['curso']
                                                ?? ''
                                            )
                                        ) ?>

                                    </span>

                                </div>

                            <?php endif; ?>


                            <div class="d-grid">

                                <a
                                    href="<?= htmlspecialchars(
                                        $urlAgendaLaboratorio
                                    ) ?>"
                                    class="btn btn-outline-primary">

                                    <i class="bi bi-calendar-week me-1"></i>

                                    Ver agenda

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

