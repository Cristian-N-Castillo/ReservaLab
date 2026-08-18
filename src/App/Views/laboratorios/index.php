<?php

declare(strict_types=1);

use Core\Session;

/** @var App\Models\Laboratorio[] $laboratorios */
/** @var bool $esAdmin */

$title = $title ?? 'Laboratorios';
$esAdmin = $esAdmin ?? false;

?>

<div class="container-fluid">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                <?= $esAdmin ? 'Gestión de Laboratorios' : 'Laboratorios' ?>
            </h2>

            <p class="text-muted mb-0">
                <?= $esAdmin
                    ? 'Administra los laboratorios computacionales del establecimiento.'
                    : 'Laboratorios disponibles en el establecimiento.' ?>
            </p>

        </div>

        <?php if ($esAdmin): ?>

            <a href="/laboratorios/crear" class="btn btn-primary">

                <i class="bi bi-plus-circle me-2"></i>

                Nuevo Laboratorio

            </a>

        <?php endif; ?>

    </div>

    <!-- Mensajes -->
    <?php if (Session::hasFlash('success')): ?>

        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            <?= htmlspecialchars(Session::getFlash('success')) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
            </button>

        </div>

    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>

        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= htmlspecialchars(Session::getFlash('error')) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
            </button>

        </div>

    <?php endif; ?>

    <!-- Tarjeta -->
    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>ID</th>

                            <th>Nombre</th>

                            <th>Ubicación</th>

                            <th>Capacidad</th>

                            <th>Especialidad prioritaria</th>

                            <?php if ($esAdmin): ?>

                                <th>Estado</th>

                            <?php endif; ?>

                            <th class="text-center">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($laboratorios)): ?>

                            <tr>

                                <td colspan="<?= $esAdmin ? 7 : 6 ?>" class="text-center py-5">

                                    <i class="bi bi-pc-display display-5 text-secondary"></i>

                                    <p class="mt-3 mb-0 text-muted">

                                        No existen laboratorios registrados.

                                    </p>

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($laboratorios as $laboratorio): ?>

                                <tr>

                                    <td><?= $laboratorio->id_laboratorio ?></td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($laboratorio->nombre) ?>
                                        </strong>
                                    </td>

                                    <td><?= htmlspecialchars($laboratorio->ubicacion) ?></td>

                                    <td><?= (int) $laboratorio->capacidad ?></td>

                                    <td>
                                        <?php if ($laboratorio->especialidad_prioritaria): ?>

                                            <?= htmlspecialchars($laboratorio->especialidad_prioritaria) ?>

                                        <?php else: ?>

                                            <span class="text-muted">—</span>

                                        <?php endif; ?>
                                    </td>

                                    <?php if ($esAdmin): ?>

                                    <td>

                                        <?php if ($laboratorio->activo): ?>

                                            <span class="badge bg-success">
                                                Activo
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-danger">
                                                Inactivo
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-center">

                                        <a
                                            href="/laboratorios/<?= $laboratorio->id_laboratorio ?>/editar"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Editar">

                                            <i class="bi bi-pencil-square"></i>

                                        </a>

                                        <?php if ($laboratorio->activo): ?>

                                            <form
                                                action="/laboratorios/<?= $laboratorio->id_laboratorio ?>/desactivar"
                                                method="POST"
                                                class="d-inline">

                                                <?= \Core\Csrf::field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar">

                                                    <i class="bi bi-toggle-off"></i>

                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <form
                                                action="/laboratorios/<?= $laboratorio->id_laboratorio ?>/activar"
                                                method="POST"
                                                class="d-inline">

                                                <?= \Core\Csrf::field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="Activar">

                                                    <i class="bi bi-toggle-on"></i>

                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </td>

                                    <?php endif; ?>

                                    <td class="text-center">

                                        <?php if ($laboratorio->activo): ?>

                                            <a
                                                href="/reservas?id_laboratorio=<?= $laboratorio->id_laboratorio ?>"
                                                class="btn btn-sm btn-primary"
                                                title="Reservar">

                                                <i class="bi bi-calendar-plus"></i>
                                                Reservar

                                            </a>

                                        <?php else: ?>

                                            <span class="text-muted small">No disponible</span>

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
