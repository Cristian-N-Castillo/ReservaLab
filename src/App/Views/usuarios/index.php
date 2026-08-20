<?php

declare(strict_types=1);

/** @var App\Models\Usuario[] $usuarios */

$title = $title ?? 'Usuarios';

?>

<div class="container-fluid">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Gestión de Usuarios
            </h2>

            <p class="text-muted mb-0">
                Administra los usuarios registrados en el sistema.
            </p>

        </div>

        <a href="/usuarios/crear" class="btn btn-primary">

            <i class="bi bi-person-plus-fill me-2"></i>

            Nuevo Usuario

        </a>

    </div>

    <!-- Tarjeta -->
    <div class="card shadow-sm border-0">

        <div class="card-body">

            <!-- Buscador -->
            <div class="row mb-4">

                <div class="col-12 col-md-5">

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            id="buscadorUsuarios"
                            class="form-control"
                            placeholder="Buscar usuario..."
                            autocomplete="off">

                    </div>

                </div>

            </div>

            <!-- Tabla -->
            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                    <tr>

                        <th>ID</th>

                        <th>RUT</th>

                        <th>Nombre</th>

                        <th>Correo</th>

                        <th>Rol</th>

                        <th>Estado</th>

                        <th class="text-center">Acciones</th>

                    </tr>

                    </thead>

                    <tbody id="tablaUsuarios">

                    <?php if (empty($usuarios)): ?>

                        <tr>

                            <td colspan="7" class="text-center py-5">

                                <i class="bi bi-people display-5 text-secondary"></i>

                                <p class="mt-3 mb-0 text-muted">

                                    No existen usuarios registrados.

                                </p>

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($usuarios as $usuario): ?>

                            <tr data-busqueda="<?= htmlspecialchars(mb_strtolower($usuario->rut . ' ' . $usuario->nombres . ' ' . $usuario->apellidos . ' ' . $usuario->correo . ' ' . $usuario->rol_nombre)) ?>">

                                <td><?= $usuario->id_usuario ?></td>

                                <td><?= htmlspecialchars($usuario->rut) ?></td>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars($usuario->nombres . ' ' . $usuario->apellidos) ?>

                                    </strong>

                                </td>

                                <td><?= htmlspecialchars($usuario->correo) ?></td>

                                <td>

                                    <?php if ($usuario->rol_nombre === 'Administrador'): ?>

                                        <span class="badge bg-primary">

                                            <?= htmlspecialchars($usuario->rol_nombre) ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-success">

                                            <?= htmlspecialchars($usuario->rol_nombre) ?>

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php if ($usuario->activo): ?>

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
                                        href="/usuarios/<?= $usuario->id_usuario ?>"
                                        class="btn btn-sm btn-outline-info"
                                        title="Ver">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                    <a
                                        href="/usuarios/<?= $usuario->id_usuario ?>/editar"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Editar">

                                        <i class="bi bi-pencil-square"></i>

                                    </a>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger btn-eliminar"
                                        title="Eliminar"
                                        data-id="<?= $usuario->id_usuario ?>"
                                        data-nombre="<?= htmlspecialchars($usuario->nombres . ' ' . $usuario->apellidos) ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

                <p id="sinResultadosUsuarios" class="text-center text-muted py-4 d-none">

                    <i class="bi bi-search"></i>
                    No se encontraron usuarios que coincidan con la búsqueda.

                </p>

            </div>

        </div>

    </div>

</div>

<!-- Modal Eliminar -->
<div
    class="modal fade"
    id="eliminarModal"
    tabindex="-1"
    aria-labelledby="eliminarModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-danger text-white">

                <h5
                    class="modal-title"
                    id="eliminarModalLabel">

                    <i class="bi bi-trash-fill me-2"></i>

                    Eliminar Usuario

                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <p>

                    ¿Está seguro que desea eliminar al usuario
                    <strong id="nombreUsuario"></strong>?

                </p>

                <div class="alert alert-warning mb-0">

                    Esta acción no se puede deshacer.

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>

                <form
                    id="formEliminar"
                    method="POST">

                    <?= \Core\Csrf::field() ?>

                    <button
                        type="submit"
                        class="btn btn-danger">

                        <i class="bi bi-trash-fill me-2"></i>

                        Eliminar

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const nombreUsuario = document.getElementById('nombreUsuario');
    const formulario = document.getElementById('formEliminar');

    document.querySelectorAll('.btn-eliminar').forEach(boton => {

        boton.addEventListener('click', () => {

            nombreUsuario.textContent = boton.dataset.nombre;

            formulario.action = '/usuarios/' + boton.dataset.id + '/eliminar';

        });

    });

    const buscador = document.getElementById('buscadorUsuarios');
    const filas = Array.from(document.querySelectorAll('#tablaUsuarios tr[data-busqueda]'));
    const sinResultados = document.getElementById('sinResultadosUsuarios');

    if (buscador) {

        buscador.addEventListener('input', () => {

            const texto = buscador.value.trim().toLowerCase();
            let visibles = 0;

            filas.forEach(fila => {

                const coincide = fila.dataset.busqueda.includes(texto);
                fila.classList.toggle('d-none', !coincide);

                if (coincide) {
                    visibles++;
                }

            });

            sinResultados.classList.toggle('d-none', visibles > 0 || filas.length === 0);

        });

    }

});

</script>