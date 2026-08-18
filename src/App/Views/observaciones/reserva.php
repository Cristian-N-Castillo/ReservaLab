<?php

declare(strict_types=1);

/** @var array $reserva */
/** @var array $observaciones */
/** @var bool $esAdmin */

$title = $title ?? 'Observaciones de la Reserva';
$esAdmin = $esAdmin ?? false;

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Observaciones — Reserva #<?= (int) $reserva['id_reserva'] ?>
            </h2>

            <p class="text-muted mb-0">
                Registre observaciones administrativas sobre esta reserva.
            </p>

        </div>

        <a href="/reservas" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Volver a Reservas
        </a>

    </div>

    <!-- Contexto de la reserva -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3">
                    <small class="text-muted d-block">Fecha</small>
                    <strong><?= htmlspecialchars((string) $reserva['fecha']) ?></strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Bloque</small>
                    <strong>
                        <?= htmlspecialchars((string) $reserva['horario']) ?>
                        (<?= substr((string) $reserva['hora_inicio'], 0, 5) ?>
                        - <?= substr((string) $reserva['hora_fin'], 0, 5) ?>)
                    </strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Laboratorio</small>
                    <strong><?= htmlspecialchars((string) $reserva['laboratorio']) ?></strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Curso</small>
                    <strong><?= htmlspecialchars((string) $reserva['curso']) ?></strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Docente</small>
                    <strong>
                        <?= htmlspecialchars(
                            trim($reserva['nombres'] . ' ' . $reserva['apellidos'])
                        ) ?>
                    </strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Estado</small>
                    <span class="badge bg-primary">
                        <?= htmlspecialchars((string) $reserva['estado']) ?>
                    </span>
                </div>

                <?php if (!empty($reserva['motivo'])): ?>

                    <div class="col-md-6">
                        <small class="text-muted d-block">Motivo</small>
                        <strong><?= htmlspecialchars((string) $reserva['motivo']) ?></strong>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <div class="row g-4">

        <?php if ($esAdmin): ?>

        <!-- Formulario nueva observación -->
        <div class="col-lg-5">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-primary text-white">
                    <i class="bi bi-journal-plus me-2"></i>
                    Nueva Observación
                </div>

                <div class="card-body">

                    <form
                        action="/reservas/<?= (int) $reserva['id_reserva'] ?>/observaciones"
                        method="POST"
                        enctype="multipart/form-data">

                        <?= \Core\Csrf::field() ?>

                        <div class="mb-3">

                            <label for="observacion" class="form-label fw-semibold">
                                Observación
                            </label>

                            <textarea
                                id="observacion"
                                name="observacion"
                                class="form-control"
                                rows="4"
                                maxlength="2000"
                                required></textarea>

                        </div>

                        <div class="mb-3">

                            <label for="archivo_pdf" class="form-label fw-semibold">
                                Adjuntar PDF (opcional)
                            </label>

                            <input
                                type="file"
                                id="archivo_pdf"
                                name="archivo_pdf"
                                class="form-control"
                                accept="application/pdf">

                            <div class="form-text">
                                Tamaño máximo 5 MB. Solo formato PDF.
                            </div>

                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-envelope me-1"></i>
                            Al guardar se notificará por correo al docente responsable de la reserva.
                        </div>

                        <div class="d-grid">

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Guardar Observación
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <?php endif; ?>

        <!-- Historial de observaciones -->
        <div class="<?= $esAdmin ? 'col-lg-7' : 'col-lg-12' ?>">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-dark text-white">
                    <i class="bi bi-clock-history me-2"></i>
                    Historial
                </div>

                <div class="card-body">

                    <?php if (empty($observaciones)): ?>

                        <div class="alert alert-secondary mb-0">
                            Aún no existen observaciones para esta reserva.
                        </div>

                    <?php else: ?>

                        <?php foreach ($observaciones as $obs): ?>

                            <div class="border rounded p-3 mb-3">

                                <div class="d-flex justify-content-between align-items-start mb-2">

                                    <strong>
                                        <?= htmlspecialchars(
                                            trim($obs['admin_nombres'] . ' ' . $obs['admin_apellidos'])
                                        ) ?>
                                    </strong>

                                    <small class="text-muted">
                                        <?= htmlspecialchars(
                                            date('d/m/Y H:i', strtotime((string) $obs['created_at']))
                                        ) ?>
                                    </small>

                                </div>

                                <p class="mb-2">
                                    <?= nl2br(htmlspecialchars((string) $obs['observacion'])) ?>
                                </p>

                                <?php if (!empty($obs['archivo_pdf'])): ?>

                                    <a
                                        href="/observaciones/<?= (int) $obs['id_observacion'] ?>/pdf"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-danger">

                                        <i class="bi bi-file-earmark-pdf me-1"></i>
                                        Ver PDF adjunto

                                    </a>

                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>
