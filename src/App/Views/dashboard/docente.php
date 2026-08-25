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
    <div class="row g-4 mb-4">

        <div class="col-12 col-sm-4">

            <div class="card stat-card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="stat-label">Mis Reservas</div>
                            <div class="stat-number"><?= $totalReservas ?></div>
                            <small class="text-secondary">Total histórico</small>
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

        <a href="/reservas" class="btn btn-success">
            <i class="bi bi-calendar-plus me-1"></i>
            Nueva Reserva
        </a>

    </div>

    <!-- Historial -->
    <div class="card dashboard-card">

        <div class="card-header">
            <i class="bi bi-clock-history me-1"></i>
            Historial de Reservas
        </div>

        <div class="card-body p-0">

            <?php if (empty($historial)): ?>

                <div class="empty-state py-5">
                    <i class="bi bi-calendar-x"></i>
                    <h5>Aún no tiene reservas</h5>
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
                                <th class="text-center">Acciones</th>
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

                                    <td class="text-center">

                                        <?php if ($estado === 'pendiente'): ?>

                                            <form
                                                method="POST"
                                                action="/reservas/<?= (int) $reserva['id_reserva'] ?>/confirmar"
                                                class="d-inline">

                                                <?= \Core\Csrf::field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-success"
                                                    title="Confirmar reserva">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>

                                            </form>

                                            <form
                                                method="POST"
                                                action="/reservas/<?= (int) $reserva['id_reserva'] ?>/cancelar"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Cancelar esta reserva?');">

                                                <?= \Core\Csrf::field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Cancelar reserva">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span class="text-muted">&mdash;</span>

                                        <?php endif; ?>

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
