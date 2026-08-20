<?php

declare(strict_types=1);

use Core\Session;

$title = $title ?? 'Reservas';
$fecha = $fecha ?? date('Y-m-d');

$cursos = $cursos ?? [];
$laboratorios = $laboratorios ?? [];
$bloques = $bloques ?? [];
$reservas = $reservas ?? [];

$esAdmin = (int) Session::get('id_rol', 0) === 1;
$usuarioActual = (int) Session::get('usuario_id', 0);

?>

<div class="container-fluid">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                <i class="bi bi-calendar-check me-2"></i>

                Gestión de Reservas

            </h2>

            <p class="text-muted mb-0">

                Seleccione una fecha, laboratorio, curso y bloque horario
                para realizar una reserva.

            </p>

        </div>

    </div>


    <!-- =====================================================
         MENSAJES FLASH
    ====================================================== -->

    <?php if (Session::hasFlash('success')): ?>

        <div
            class="alert alert-success alert-dismissible fade show shadow-sm"
            role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            <?= htmlspecialchars(
                (string) Session::getFlash('success')
            ) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
            </button>

        </div>

    <?php endif; ?>


    <?php if (Session::hasFlash('error')): ?>

        <div
            class="alert alert-danger alert-dismissible fade show shadow-sm"
            role="alert">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= htmlspecialchars(
                (string) Session::getFlash('error')
            ) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
            </button>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->

    <div class="row g-4">

        <!-- =================================================
             FORMULARIO
        ================================================== -->

        <div class="col-lg-5">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-primary text-white py-3">

                    <h5 class="mb-0">

                        <i class="bi bi-calendar-plus me-2"></i>

                        Nueva Reserva

                    </h5>

                </div>

                <div class="card-body">

                    <form
                        action="/reservas"
                        method="POST"
                        id="formReserva">

                        <?= \Core\Csrf::field() ?>

                        <!-- Fecha -->
                        <div class="mb-3">

                            <label
                                for="fecha"
                                class="form-label fw-semibold">

                                Fecha

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-calendar3"></i>
                                </span>

                                <input
                                    type="text"
                                    name="fecha"
                                    id="fecha"
                                    class="form-control"
                                    value="<?= htmlspecialchars($fecha) ?>"
                                    autocomplete="off"
                                    required>

                            </div>

                        </div>


                        <!-- Laboratorio -->
<div class="mb-3">

    <label
        for="id_laboratorio"
        class="form-label fw-semibold">

        Laboratorio

    </label>

    <select
        name="id_laboratorio"
        id="id_laboratorio"
        class="form-select"
        required>

        <?php if (empty($laboratorios)): ?>

            <option value="">

                No existen laboratorios disponibles

            </option>

        <?php else: ?>

            <?php foreach ($laboratorios as $laboratorio): ?>

                <?php

                $idLaboratorioActual =
                    (int) $laboratorio->id_laboratorio;

                ?>

                <option
                    value="<?= $idLaboratorioActual ?>"
                    <?= $idLaboratorioActual ===
                        (int) $idLaboratorioSeleccionado
                        ? 'selected'
                        : '' ?>>

                    <?= htmlspecialchars(
                        $laboratorio->nombre
                    ) ?>

                </option>

            <?php endforeach; ?>

        <?php endif; ?>

    </select>

</div>


                        <!-- Curso -->
                        <div class="mb-3">

                            <label
                                for="id_curso"
                                class="form-label fw-semibold">

                                Curso

                            </label>

                            <select
                                name="id_curso"
                                id="id_curso"
                                class="form-select"
                                required>

                                <option value="">

                                    Seleccione un curso

                                </option>

                                <?php foreach ($cursos as $curso): ?>

                                    <?php if ($curso->activo): ?>

                                        <option
                                            value="<?= (int) $curso->id_curso ?>">

                                            <?= htmlspecialchars(
                                                $curso->nombre
                                            ) ?>

                                        </option>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Bloque seleccionado -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Bloque seleccionado

                            </label>

                            <div
                                id="bloqueSeleccionado"
                                class="alert alert-secondary mb-2">

                                <i class="bi bi-clock me-2"></i>

                                Aún no ha seleccionado un bloque horario.

                            </div>

                            <input
                                type="hidden"
                                name="id_horario"
                                id="id_horario"
                                value="">

                        </div>


                        <!-- Motivo -->
                        <div class="mb-4">

                            <label
                                for="motivo"
                                class="form-label fw-semibold">

                                Motivo

                            </label>

                            <textarea
                                name="motivo"
                                id="motivo"
                                class="form-control"
                                rows="4"
                                maxlength="500"
                                placeholder="Escriba el motivo de la reserva..."></textarea>

                            <div class="form-text">

                                Indique brevemente el motivo o actividad
                                que realizará en el laboratorio.

                            </div>

                        </div>


                        <!-- Botón reservar -->
                        <div class="d-grid">

                            <button
                                type="submit"
                                class="btn btn-success btn-lg"
                                id="btnReservar"
                                disabled>

                                <i class="bi bi-check-circle-fill me-2"></i>

                                Realizar Reserva

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =================================================
             BLOQUES HORARIOS
        ================================================== -->

        <div class="col-lg-7">

            <div class="card shadow-sm border-0 h-100">

                <div
                    class="card-header bg-success text-white py-3
                           d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        <i class="bi bi-clock-history me-2"></i>

                        Bloques Horarios

                    </h5>

                    <span class="badge bg-light text-dark">

                        <?= date(
                            'd/m/Y',
                            strtotime($fecha)
                        ) ?>

                    </span>

                </div>

                <div class="card-body">

                    <?php if (empty($laboratorios)): ?>

                        <div class="alert alert-warning mb-0">

                            <i class="bi bi-exclamation-triangle-fill me-2"></i>

                            No existen laboratorios activos.

                        </div>

                    <?php elseif (empty($bloques)): ?>

                        <div class="alert alert-secondary mb-0">

                            <i class="bi bi-calendar-x me-2"></i>

                            No existen bloques horarios disponibles.

                        </div>

                    <?php else: ?>

                        <div class="row g-3">

                            <?php foreach ($bloques as $bloque): ?>

                                <?php

                                $reservado = filter_var(
                                    $bloque['reservado'] ?? false,
                                    FILTER_VALIDATE_BOOLEAN
                                );

                                $horaInicio = isset($bloque['hora_inicio'])
                                    ? substr(
                                        (string) $bloque['hora_inicio'],
                                        0,
                                        5
                                    )
                                    : '';

                                $horaFin = isset($bloque['hora_fin'])
                                    ? substr(
                                        (string) $bloque['hora_fin'],
                                        0,
                                        5
                                    )
                                    : '';

                                ?>

                                <div class="col-md-6">

                                    <div
                                        class="card h-100 <?= $reservado
                                            ? 'border-danger'
                                            : 'border-success' ?>">

                                        <div class="card-body">

                                            <div
                                                class="d-flex
                                                       justify-content-between
                                                       align-items-start
                                                       mb-3">

                                                <div>

                                                    <h5 class="fw-bold mb-1">

                                                        <?= htmlspecialchars(
                                                            (string) (
                                                                $bloque['nombre']
                                                                ?? 'Bloque'
                                                            )
                                                        ) ?>

                                                    </h5>

                                                    <div class="text-muted">

                                                        <i class="bi bi-clock me-1"></i>

                                                        <?= htmlspecialchars(
                                                            $horaInicio
                                                        ) ?>

                                                        -

                                                        <?= htmlspecialchars(
                                                            $horaFin
                                                        ) ?>

                                                    </div>

                                                </div>


                                                <?php if ($reservado): ?>

                                                    <span class="badge bg-danger">

                                                        <i class="bi bi-x-circle me-1"></i>

                                                        Reservado

                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge bg-success">

                                                        <i class="bi bi-check-circle me-1"></i>

                                                        Disponible

                                                    </span>

                                                <?php endif; ?>

                                            </div>


                                            <?php if ($reservado): ?>

                                                <hr>

                                                <?php if (!empty($bloque['curso'])): ?>

                                                    <div class="mb-2">

                                                        <small class="text-muted">

                                                            Curso

                                                        </small>

                                                        <div class="fw-semibold">

                                                            <?= htmlspecialchars(
                                                                (string) $bloque['curso']
                                                            ) ?>

                                                        </div>

                                                    </div>

                                                <?php endif; ?>


                                                <?php

                                                $nombreResponsable = trim(
                                                    (
                                                        $bloque['nombres']
                                                        ?? ''
                                                    )
                                                    . ' '
                                                    . (
                                                        $bloque['apellidos']
                                                        ?? ''
                                                    )
                                                );

                                                ?>

                                                <?php if ($nombreResponsable !== ''): ?>

                                                    <div class="mb-2">

                                                        <small class="text-muted">

                                                            Responsable

                                                        </small>

                                                        <div>

                                                            <i class="bi bi-person me-1"></i>

                                                            <?= htmlspecialchars(
                                                                $nombreResponsable
                                                            ) ?>

                                                        </div>

                                                    </div>

                                                <?php endif; ?>


                                                <?php if (!empty($bloque['estado'])): ?>

                                                    <div class="mt-3">

                                                        <span class="badge bg-secondary">

                                                            <?= htmlspecialchars(
                                                                (string) $bloque['estado']
                                                            ) ?>

                                                        </span>

                                                    </div>

                                                <?php endif; ?>


                                            <?php else: ?>

                                                <hr>

                                                <div class="d-grid">

                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-success btn-seleccionar-bloque"
                                                        data-id="<?= (int) $bloque['id_horario'] ?>"
                                                        data-nombre="<?= htmlspecialchars(
                                                            (string) (
                                                                $bloque['nombre']
                                                                ?? 'Bloque'
                                                            )
                                                        ) ?>"
                                                        data-inicio="<?= htmlspecialchars(
                                                            $horaInicio
                                                        ) ?>"
                                                        data-fin="<?= htmlspecialchars(
                                                            $horaFin
                                                        ) ?>">

                                                        <i class="bi bi-check2-circle me-2"></i>

                                                        Seleccionar

                                                    </button>

                                                </div>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         RESERVAS DEL DÍA
    ====================================================== -->

    <div class="row mt-4">

        <div class="col-12">

            <div class="card shadow-sm border-0">

                <div
                    class="card-header bg-dark text-white py-3
                           d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        <i class="bi bi-list-check me-2"></i>

                        Reservas del Día

                    </h5>

                    <span class="badge bg-light text-dark">

                        <?= count($reservas) ?>

                        <?= count($reservas) === 1
                            ? 'reserva'
                            : 'reservas' ?>

                    </span>

                </div>


                <div class="card-body">

                    <?php if (empty($reservas)): ?>

                        <div class="alert alert-secondary mb-0">

                            <i class="bi bi-info-circle-fill me-2"></i>

                            Aún no existen reservas para la fecha seleccionada.

                        </div>

                    <?php else: ?>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>Bloque</th>

                                        <th>Horario</th>

                                        <th>Laboratorio</th>

                                        <th>Curso</th>

                                        <th>Responsable</th>

                                        <th>Estado</th>

                                        <th>Motivo</th>

                                        <th class="text-center">Observaciones</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($reservas as $reserva): ?>

                                        <?php

                                        $horaInicio = isset(
                                            $reserva['hora_inicio']
                                        )
                                            ? substr(
                                                (string) $reserva['hora_inicio'],
                                                0,
                                                5
                                            )
                                            : '';

                                        $horaFin = isset(
                                            $reserva['hora_fin']
                                        )
                                            ? substr(
                                                (string) $reserva['hora_fin'],
                                                0,
                                                5
                                            )
                                            : '';

                                        $responsable = trim(
                                            (
                                                $reserva['nombres']
                                                ?? ''
                                            )
                                            . ' '
                                            . (
                                                $reserva['apellidos']
                                                ?? ''
                                            )
                                        );

                                        ?>

                                        <tr>

                                            <td>

                                                <strong>

                                                    Bloque <?= htmlspecialchars(
                                                        (string) (
                                                            $reserva['bloque']
                                                            ?? ''
                                                        )
                                                    ) ?>

                                                </strong>

                                            </td>


                                            <td>

                                                <i class="bi bi-clock me-1"></i>

                                                <?= htmlspecialchars(
                                                    $horaInicio
                                                ) ?>

                                                -

                                                <?= htmlspecialchars(
                                                    $horaFin
                                                ) ?>

                                            </td>


                                            <td>

                                                <i class="bi bi-pc-display me-1"></i>

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $reserva['laboratorio']
                                                        ?? ''
                                                    )
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $reserva['curso']
                                                        ?? ''
                                                    )
                                                ) ?>

                                            </td>


                                            <td>

                                                <i class="bi bi-person me-1"></i>

                                                <?= htmlspecialchars(
                                                    $responsable
                                                ) ?>

                                            </td>


                                            <td>

                                                <span class="badge bg-primary">

                                                    <?= htmlspecialchars(
                                                        (string) (
                                                            $reserva['estado']
                                                            ?? ''
                                                        )
                                                    ) ?>

                                                </span>

                                            </td>


                                            <td>

                                                <?php if (!empty($reserva['motivo'])): ?>

                                                    <?= htmlspecialchars(
                                                        (string) $reserva['motivo']
                                                    ) ?>

                                                <?php else: ?>

                                                    <span class="text-muted">

                                                        Sin motivo registrado

                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td class="text-center">

                                                <?php if ($esAdmin || $usuarioActual === (int) ($reserva['id_usuario'] ?? 0)): ?>

                                                    <a
                                                        href="/reservas/<?= (int) $reserva['id_reserva'] ?>/observaciones"
                                                        class="btn btn-sm btn-outline-dark"
                                                        title="Ver observaciones">

                                                        <i class="bi bi-journal-text"></i>

                                                    </a>

                                                <?php else: ?>

                                                    <span class="text-muted">—</span>

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

    </div>

</div>


<!-- Selector de fecha (Flatpickr): permite deshabilitar sábado y domingo
     visualmente en el calendario, algo que el <input type="date"> nativo
     del navegador no admite. -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/es.js"></script>

<style>
    /* El calendario de Flatpickr se ve más grande que el selector
       nativo del navegador; lo reducimos para que calce mejor con
       el resto del formulario. */
    .flatpickr-calendar {
        transform: scale(0.85);
        transform-origin: top left;
        font-size: 13px;
    }
</style>

<!-- JavaScript específico del módulo Reservas -->
<script src="/assets/js/reservas.js"></script>