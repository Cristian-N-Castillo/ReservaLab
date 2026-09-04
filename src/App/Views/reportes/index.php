<?php

declare(strict_types=1);

/** @var string $fechaDesde */
/** @var string $fechaHasta */
/** @var array $porLaboratorio */
/** @var array $porDocente */
/** @var array $porCurso */
/** @var array $masUtilizados */

$title = $title ?? 'Reportes';

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Reportes
            </h2>

            <p class="text-muted mb-0">
                Estadísticas de uso de laboratorios, cursos y docentes.
            </p>

        </div>

    </div>

    <!-- Filtro de fechas -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <form action="/reportes" method="GET" class="row g-3 align-items-end">

                <div class="col-6 col-md-3">
                    <label for="fecha_desde" class="form-label fw-semibold">Desde</label>
                    <input
                        type="date"
                        id="fecha_desde"
                        name="fecha_desde"
                        class="form-control"
                        value="<?= htmlspecialchars($fechaDesde) ?>">
                </div>

                <div class="col-6 col-md-3">
                    <label for="fecha_hasta" class="form-label fw-semibold">Hasta</label>
                    <input
                        type="date"
                        id="fecha_hasta"
                        name="fecha_hasta"
                        class="form-control"
                        value="<?= htmlspecialchars($fechaHasta) ?>">
                </div>

                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel-fill me-2"></i>
                        Filtrar
                    </button>

                    <a href="/reportes" class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>

            </form>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <!-- Reservas por laboratorio -->
        <div class="col-12 col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-primary text-white">
                    <i class="bi bi-pc-display me-2"></i>
                    Reservas por Laboratorio
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Laboratorio</th>
                                    <th class="text-end">Reservas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($porLaboratorio)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Sin datos</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($porLaboratorio as $fila): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $fila['laboratorio']) ?></td>
                                            <td class="text-end"><?= (int) $fila['total'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

        </div>

        <!-- Reservas por docente -->
        <div class="col-12 col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-success text-white">
                    <i class="bi bi-person-badge me-2"></i>
                    Reservas por Docente
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Docente</th>
                                    <th class="text-end">Reservas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($porDocente)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Sin datos</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($porDocente as $fila): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars(
                                                    trim($fila['nombres'] . ' ' . $fila['apellidos'])
                                                ) ?>
                                            </td>
                                            <td class="text-end"><?= (int) $fila['total'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

        </div>

        <!-- Reservas por curso -->
        <div class="col-12 col-md-6 col-lg-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-info text-white">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Reservas por Curso
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Curso</th>
                                    <th class="text-end">Reservas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($porCurso)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Sin datos</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($porCurso as $fila): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $fila['curso']) ?></td>
                                            <td class="text-end"><?= (int) $fila['total'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Laboratorios más utilizados -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-warning">
            <i class="bi bi-trophy-fill me-2"></i>
            Laboratorios Más Utilizados
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px;">#</th>
                            <th>Laboratorio</th>
                            <th class="text-end">Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($masUtilizados)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Sin datos</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($masUtilizados as $indice => $fila): ?>
                                <tr>
                                    <td><?= $indice + 1 ?></td>
                                    <td><?= htmlspecialchars((string) $fila['laboratorio']) ?></td>
                                    <td class="text-end"><?= (int) $fila['total'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>

        </div>

    </div>

</div>
