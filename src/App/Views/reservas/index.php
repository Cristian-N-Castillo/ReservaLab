<?php

declare(strict_types=1);

use Core\Session;

$title = $title ?? 'Reservas';
$fecha = $fecha ?? date('Y-m-d');

$cursos = $cursos ?? [];
$laboratorios = $laboratorios ?? [];
$bloques = $bloques ?? [];

$esAdmin = (int) Session::get('id_rol', 0) === 1;

$mostrarTutorialReservas = !$esAdmin
    && !in_array('reservas', Session::get('tutoriales_vistos', []), true);

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

                Seleccione una fecha, laboratorio, curso y hasta 3 bloques
                horarios para realizar una reserva.

            </p>

        </div>

        <button
            type="button"
            id="btnVerGuiaReservas"
            class="btn btn-outline-primary"
            onclick="window.iniciarTourGuiado && window.iniciarTourGuiado()">

            <i class="bi bi-question-circle me-2"></i>
            Ver guía

        </button>

    </div>



    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->

    <div class="row g-4">

        <!-- =================================================
             FORMULARIO
        ================================================== -->

        <div class="col-12 col-lg-5 order-2 order-lg-1">

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


                        <!-- Bloques seleccionados -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Bloques seleccionados (máx. 3)

                            </label>

                            <div
                                id="bloqueSeleccionado"
                                class="alert alert-secondary mb-2">

                                <i class="bi bi-clock me-2"></i>

                                Aún no ha seleccionado ningún bloque horario.

                            </div>

                            <div id="idsHorarioContainer"></div>

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

        <div class="col-12 col-lg-7 order-1 order-lg-2">

            <div id="tour-bloques-horarios" class="card shadow-sm border-0 h-100">

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

                                <div class="col-12 col-md-6">

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

    /* Flatpickr trae su propio tema (azul) independiente de
       Bootstrap; se sobreescribe el color del día seleccionado
       para que coincida con el verde institucional. */
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange,
    .flatpickr-day.selected.inRange,
    .flatpickr-day.startRange.inRange,
    .flatpickr-day.endRange.inRange,
    .flatpickr-day.selected:focus,
    .flatpickr-day.startRange:focus,
    .flatpickr-day.endRange:focus,
    .flatpickr-day.selected:hover,
    .flatpickr-day.startRange:hover,
    .flatpickr-day.endRange:hover {
        background: #1B6B3B;
        border-color: #1B6B3B;
    }
</style>

<!-- JavaScript específico del módulo Reservas -->
<script src="<?= \Core\Asset::url('/assets/js/reservas.js') ?>"></script>

<!--
    Tour guiado del módulo Reservas: se auto-inicia solo la primera vez
    que un Docente entra (mostrarTutorialReservas), pero el script queda
    disponible siempre para que el botón "Ver guía" del header pueda
    relanzarlo manualmente en cualquier momento, para cualquier rol.
-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css">
<link rel="stylesheet" href="<?= \Core\Asset::url('/assets/css/tour.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>

<script>
    window.tourConfig = {
        modulo: 'reservas',
        mostrar: <?= $mostrarTutorialReservas ? 'true' : 'false' ?>,
        token: '<?= addslashes(\Core\Csrf::token()) ?>',
        steps: [
            {
                popover: {
                    title: '📅 Cómo reservar un laboratorio',
                    description: 'Te mostramos rápidamente cómo crear una reserva. Puedes cerrar este recorrido en cualquier momento.'
                }
            },
            {
                element: '.tour-fecha-visible',
                popover: {
                    title: 'Fecha',
                    description: 'Elige el día en que necesitas el laboratorio.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#id_laboratorio',
                popover: {
                    title: 'Laboratorio',
                    description: 'Elige qué laboratorio vas a usar.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#id_curso',
                popover: {
                    title: 'Curso',
                    description: 'Elige el curso con el que asistirás.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#tour-bloques-horarios',
                popover: {
                    title: 'Bloques Horarios',
                    description: 'Aquí ves los bloques disponibles para la fecha y laboratorio elegidos. Puedes seleccionar hasta 3 haciendo clic en "Seleccionar".',
                    side: 'left',
                    align: 'start'
                }
            },
            {
                element: '#bloqueSeleccionado',
                popover: {
                    title: 'Resumen',
                    description: 'Aquí verás los bloques que vayas seleccionando.',
                    side: 'top',
                    align: 'start'
                }
            },
            {
                element: '#motivo',
                popover: {
                    title: 'Motivo',
                    description: 'Cuéntanos brevemente qué actividad realizarán en el laboratorio.',
                    side: 'top',
                    align: 'start'
                }
            },
            {
                element: '#btnReservar',
                popover: {
                    title: 'Realizar Reserva',
                    description: 'Cuando termines, presiona aquí. Te llegará un correo para confirmar o cancelar la reserva.',
                    side: 'top',
                    align: 'start'
                }
            },
            {
                popover: {
                    title: '✅ ¡Listo!',
                    description: 'Ya sabes cómo reservar un laboratorio. Recuerda que puedes hacerlo también desde el botón "Reservar" en la pantalla de Laboratorios.'
                }
            }
        ]
    };
</script>

<script src="<?= \Core\Asset::url('/assets/js/tutorial.js') ?>"></script>