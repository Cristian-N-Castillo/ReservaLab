<?php

declare(strict_types=1);

/** @var array $observaciones */
/** @var array $reservasDelDia */
/** @var string $fechaReservas */
/** @var bool $abrirSelector */
/** @var array $laboratorios */
/** @var int $idLaboratorioSeleccionado */

$title = $title ?? 'Observaciones';

$reservasDelDia = $reservasDelDia ?? [];
$fechaReservas = $fechaReservas ?? date('Y-m-d');
$abrirSelector = $abrirSelector ?? false;
$laboratorios = $laboratorios ?? [];
$idLaboratorioSeleccionado = (int) ($idLaboratorioSeleccionado ?? 0);

function horaObservaciones(?string $hora): string
{
    return empty($hora) ? '' : substr($hora, 0, 5);
}

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Observaciones
            </h2>

            <p class="text-muted mb-0">
                Observaciones registradas sobre las reservas del sistema.
            </p>

        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalIngresarObservacion">

            <i class="bi bi-journal-plus me-2"></i>
            Ingresar observación

        </button>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Fecha registro</th>
                            <th>Reserva</th>
                            <th>Laboratorio</th>
                            <th>Curso</th>
                            <th>Docente</th>
                            <th>Registrada por</th>
                            <th>Observación</th>
                            <th class="text-center">PDF</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($observaciones)): ?>

                            <tr>
                                <td colspan="8" class="text-center py-5">

                                    <i class="bi bi-journal-x display-5 text-secondary"></i>

                                    <p class="mt-3 mb-0 text-muted">
                                        No existen observaciones registradas.
                                    </p>

                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($observaciones as $obs): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            date('d/m/Y H:i', strtotime((string) $obs['created_at']))
                                        ) ?>
                                    </td>

                                    <td>
                                        <a href="/reservas/<?= (int) $obs['id_reserva'] ?>/observaciones">
                                            #<?= (int) $obs['id_reserva'] ?>
                                            (<?= htmlspecialchars((string) $obs['fecha']) ?>)
                                        </a>
                                    </td>

                                    <td><?= htmlspecialchars((string) $obs['laboratorio']) ?></td>

                                    <td><?= htmlspecialchars((string) $obs['curso']) ?></td>

                                    <td>
                                        <?= htmlspecialchars(
                                            trim($obs['docente_nombres'] . ' ' . $obs['docente_apellidos'])
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            trim($obs['admin_nombres'] . ' ' . $obs['admin_apellidos'])
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= nl2br(htmlspecialchars((string) $obs['observacion'])) ?>
                                    </td>

                                    <td class="text-center">

                                        <?php if (!empty($obs['archivo_pdf'])): ?>

                                            <a
                                                href="/observaciones/<?= (int) $obs['id_observacion'] ?>/pdf"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Ver PDF">

                                                <i class="bi bi-file-earmark-pdf"></i>

                                            </a>

                                        <?php else: ?>

                                            <span class="text-muted">—</span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     Selector de reserva para registrar una observación
====================================================== -->

<div class="modal fade" id="modalIngresarObservacion" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="bi bi-journal-plus me-2"></i>
                    Ingresar observación
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>

            </div>

            <div class="modal-body">

                <p class="text-muted">
                    Elija la reserva sobre la que desea registrar la observación.
                </p>

                <!-- Cambiar el día o el laboratorio recarga la pantalla
                     con esas reservas y vuelve a abrir este selector. -->
                <form method="GET" action="/observaciones" class="row g-2 align-items-end mb-4">

                    <div class="col-12 col-sm-auto">

                        <label for="fechaReservas" class="form-label fw-semibold mb-1">
                            Día
                        </label>

                        <input
                            type="date"
                            name="fecha"
                            id="fechaReservas"
                            class="form-control"
                            value="<?= htmlspecialchars($fechaReservas) ?>"
                            onchange="this.form.submit()">

                    </div>

                    <div class="col-12 col-sm">

                        <label for="laboratorioReservas" class="form-label fw-semibold mb-1">
                            Laboratorio
                        </label>

                        <select
                            name="id_laboratorio"
                            id="laboratorioReservas"
                            class="form-select"
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

                    </div>

                    <div class="col-12 col-sm-auto">

                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>
                            Ver reservas
                        </button>

                    </div>

                </form>

                <?php if (empty($reservasDelDia)): ?>

                    <div class="alert alert-secondary mb-0">

                        <i class="bi bi-calendar-x me-2"></i>

                        No existen reservas para el
                        <?= htmlspecialchars(date('d/m/Y', strtotime($fechaReservas))) ?><?php

                        // Si hay un laboratorio filtrado se aclara, para que no
                        // parezca que el día está libre por completo.
                        if ($idLaboratorioSeleccionado > 0):

                            foreach ($laboratorios as $laboratorio):

                                if ((int) $laboratorio->id_laboratorio === $idLaboratorioSeleccionado): ?>
                                    en <?= htmlspecialchars($laboratorio->nombre) ?><?php
                                endif;

                            endforeach;

                        endif;

                        ?>.

                    </div>

                <?php else: ?>

                    <div class="list-group">

                        <?php foreach ($reservasDelDia as $reserva): ?>

                            <?php

                            $estado = mb_strtolower((string) ($reserva['estado'] ?? ''));

                            $badgeEstado = match ($estado) {
                                'pendiente'  => 'bg-warning text-dark',
                                'confirmada' => 'bg-success',
                                'finalizada' => 'bg-secondary',
                                'cancelada'  => 'bg-danger',
                                default      => 'bg-secondary'
                            };

                            ?>

                            <a
                                href="/reservas/<?= (int) $reserva['id_reserva'] ?>/observaciones"
                                class="list-group-item list-group-item-action py-3">

                                <div class="d-flex justify-content-between align-items-start gap-3">

                                    <div>

                                        <div class="fw-semibold mb-1">

                                            <i class="bi bi-clock me-1"></i>

                                            <?= htmlspecialchars(
                                                horaObservaciones($reserva['hora_inicio'] ?? null)
                                            ) ?>
                                            -
                                            <?= htmlspecialchars(
                                                horaObservaciones($reserva['hora_fin'] ?? null)
                                            ) ?>

                                            <span class="text-muted fw-normal ms-2">
                                                Bloque <?= (int) ($reserva['bloque'] ?? 0) ?>
                                            </span>

                                        </div>

                                        <div class="small">

                                            <i class="bi bi-pc-display me-1"></i>
                                            <?= htmlspecialchars((string) $reserva['laboratorio']) ?>

                                            <span class="mx-1">·</span>

                                            <i class="bi bi-mortarboard me-1"></i>
                                            <?= htmlspecialchars((string) $reserva['curso']) ?>

                                        </div>

                                        <div class="small text-muted mt-1">

                                            <i class="bi bi-person me-1"></i>

                                            <?= htmlspecialchars(trim(
                                                $reserva['nombres'] . ' ' . $reserva['apellidos']
                                            )) ?>

                                        </div>

                                    </div>

                                    <div class="text-end text-nowrap">

                                        <span class="badge <?= $badgeEstado ?> mb-2">
                                            <?= htmlspecialchars((string) $reserva['estado']) ?>
                                        </span>

                                        <div class="small text-primary fw-semibold">
                                            Registrar
                                            <i class="bi bi-chevron-right"></i>
                                        </div>

                                    </div>

                                </div>

                            </a>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php if ($abrirSelector): ?>

    <!-- Se llegó aquí desde el propio selector (al cambiar de día),
         así que se vuelve a abrir para no perder el hilo. -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalIngresarObservacion');

            if (modal && window.bootstrap) {
                new bootstrap.Modal(modal).show();
            }
        });
    </script>

<?php endif; ?>
