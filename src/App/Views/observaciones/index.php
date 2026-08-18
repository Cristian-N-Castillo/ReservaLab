<?php

declare(strict_types=1);

/** @var array $observaciones */

$title = $title ?? 'Observaciones';

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Observaciones
            </h2>

            <p class="text-muted mb-0">
                Observaciones registradas sobre las reservas del sistema.
            </p>

        </div>

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
