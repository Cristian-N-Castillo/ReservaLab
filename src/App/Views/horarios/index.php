<?php

declare(strict_types=1);

/** @var App\Models\Horario[] $horarios */

$title = $title ?? 'Horarios';

?>

<div class="container-fluid">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Horarios del Establecimiento
            </h2>

            <p class="text-muted mb-0">
                Bloques horarios oficiales utilizados para las reservas de laboratorio.
            </p>

        </div>

    </div>

    <?php if (empty($horarios)): ?>

        <div class="card shadow-sm border-0">

            <div class="card-body text-center py-5">

                <i class="bi bi-clock display-3 text-secondary"></i>

                <h4 class="mt-3">

                    No existen horarios registrados.

                </h4>

                <p class="text-muted mb-0">

                    Contacte al administrador del sistema.

                </p>

            </div>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php foreach ($horarios as $horario): ?>

                <div class="col-xl-4 col-lg-4 col-md-6">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-primary text-white">

                            <div class="d-flex justify-content-between align-items-center">

                                <h5 class="mb-0">

                                    <i class="bi bi-clock-history me-2"></i>

                                    <?= htmlspecialchars($horario->nombre) ?>

                                </h5>

                                <span class="badge bg-light text-primary">

                                    <?= $horario->activo ? 'Activo' : 'Inactivo' ?>

                                </span>

                            </div>

                        </div>

                        <div class="card-body">

                            <div class="text-center">

                                <div class="display-6 fw-bold">

                                    <?= substr($horario->hora_inicio, 0, 5) ?>

                                </div>

                                <div class="my-3">

                                    <i class="bi bi-arrow-down fs-2 text-primary"></i>

                                </div>

                                <div class="display-6 fw-bold">

                                    <?= substr($horario->hora_fin, 0, 5) ?>

                                </div>

                            </div>

                        </div>

                        <div class="card-footer bg-white">

                            <small class="text-muted">

                                Horario oficial del establecimiento.

                            </small>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>