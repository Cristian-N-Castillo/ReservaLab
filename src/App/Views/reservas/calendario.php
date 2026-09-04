<?php

declare(strict_types=1);

use Core\Session;

/**
 * @var array $semanas
 * @var \DateTimeImmutable $nombreMes
 * @var string $hoy
 * @var int $anioAnterior
 * @var int $mesAnterior
 * @var int $anioSiguiente
 * @var int $mesSiguiente
 * @var array $laboratorios
 * @var int $idLaboratorioSeleccionado
 */

$esAdmin = (int) Session::get('id_rol', 0) === 1;
$usuarioActual = (int) Session::get('usuario_id', 0);

$laboratorios = $laboratorios ?? [];
$idLaboratorioSeleccionado = (int) ($idLaboratorioSeleccionado ?? 0);

/*
 * El filtro de laboratorio tiene que sobrevivir al cambiar de mes,
 * así que se arrastra en los enlaces de navegación.
 */
$filtroLaboratorio = $idLaboratorioSeleccionado > 0
    ? '&id_laboratorio=' . $idLaboratorioSeleccionado
    : '';

// Solo días hábiles: no se puede reservar sábado ni domingo.
$diasSemana = ['Lu', 'Ma', 'Mi', 'Ju', 'Vi'];

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
];

/**
 * Determina la clase de color según el estado de la reserva.
 * El calendario solo recibe reservas Pendientes o Confirmadas
 * (ReservaService::porRangoFechas ya descarta el resto).
 */
function claseEventoCalendario(array $reserva): string
{
    $estado = mb_strtolower((string) $reserva['estado']);

    if ($estado === 'pendiente') {
        return 'evento-pendiente';
    }

    return 'evento-confirmada';
}

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar3 me-2"></i>
                Calendario de Reservas
            </h2>

            <p class="text-muted mb-0">
                Vista mensual de todas las reservas del sistema.
            </p>

        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">

            <!-- Filtro por laboratorio: recarga el mes que se está
                 viendo, acotado a un solo laboratorio. -->
            <form method="GET" action="/reservas/calendario" class="d-flex align-items-center gap-2">

                <input type="hidden" name="anio" value="<?= (int) $nombreMes->format('Y') ?>">
                <input type="hidden" name="mes" value="<?= (int) $nombreMes->format('n') ?>">

                <label for="filtroLaboratorio" class="form-label mb-0 text-muted small text-nowrap">
                    <i class="bi bi-funnel me-1"></i>
                    Laboratorio
                </label>

                <select
                    name="id_laboratorio"
                    id="filtroLaboratorio"
                    class="form-select form-select-sm"
                    onchange="this.form.submit()">

                    <option value="0">
                        Todos los laboratorios
                    </option>

                    <?php foreach ($laboratorios as $laboratorio): ?>

                        <?php $idLab = (int) $laboratorio->id_laboratorio; ?>

                        <option
                            value="<?= $idLab ?>"
                            <?= $idLab === $idLaboratorioSeleccionado ? 'selected' : '' ?>>

                            <?= htmlspecialchars($laboratorio->nombre) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </form>

            <a href="/reservas" class="btn btn-primary text-nowrap">
                <i class="bi bi-calendar-plus me-2"></i>
                Nueva Reserva
            </a>

        </div>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">

            <h4 class="mb-0 text-capitalize">
                <?= htmlspecialchars($meses[(int) $nombreMes->format('n')]) ?>
                <?= htmlspecialchars($nombreMes->format('Y')) ?>
            </h4>

            <div class="btn-group">

                <a
                    href="/reservas/calendario?anio=<?= $anioAnterior ?>&mes=<?= $mesAnterior ?><?= $filtroLaboratorio ?>"
                    class="btn btn-outline-secondary btn-sm"
                    title="Mes anterior">
                    <i class="bi bi-chevron-left"></i>
                </a>

                <a
                    href="/reservas/calendario<?= $filtroLaboratorio !== ''
                        ? '?id_laboratorio=' . $idLaboratorioSeleccionado
                        : '' ?>"
                    class="btn btn-outline-secondary btn-sm">
                    Hoy
                </a>

                <a
                    href="/reservas/calendario?anio=<?= $anioSiguiente ?>&mes=<?= $mesSiguiente ?><?= $filtroLaboratorio ?>"
                    class="btn btn-outline-secondary btn-sm"
                    title="Mes siguiente">
                    <i class="bi bi-chevron-right"></i>
                </a>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="calendario-scroll">

                <div class="calendario-grid calendario-grid-compacto">

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

                                    $puedeVer = $esAdmin
                                        || $usuarioActual === (int) $reserva['id_usuario'];

                                    $tituloEvento = substr((string) $reserva['hora_inicio'], 0, 5)
                                        . ' - ' . (string) $reserva['curso']
                                        . ' - ' . (string) $reserva['laboratorio']
                                        . ' - ' . trim($reserva['nombres'] . ' ' . $reserva['apellidos'])
                                        . ' (' . (string) $reserva['estado'] . ')';

                                    $textoEvento = htmlspecialchars(substr((string) $reserva['hora_inicio'], 0, 5))
                                        . ' &middot; ' . htmlspecialchars((string) $reserva['curso']);

                                    $claseEvento = 'calendario-evento '
                                        . claseEventoCalendario($reserva);

                                    ?>

                                    <?php if ($puedeVer): ?>

                                        <a
                                            href="/reservas/<?= (int) $reserva['id_reserva'] ?>/observaciones"
                                            class="<?= $claseEvento ?>"
                                            title="<?= htmlspecialchars($tituloEvento) ?>">

                                            <?= $textoEvento ?>

                                        </a>

                                    <?php else: ?>

                                        <span
                                            class="<?= $claseEvento ?>"
                                            title="<?= htmlspecialchars($tituloEvento) ?>">

                                            <?= $textoEvento ?>

                                        </span>

                                    <?php endif; ?>

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
