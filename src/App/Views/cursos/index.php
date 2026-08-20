<?php

declare(strict_types=1);

/** @var App\Models\Curso[] $cursos */

$title = $title ?? 'Cursos';

?>

<div class="container-fluid">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Gestión de Cursos
            </h2>

            <p class="text-muted mb-0">
                Administra los cursos registrados en el establecimiento.
            </p>

        </div>

        <a href="/cursos/create" class="btn btn-primary">

            <i class="bi bi-plus-circle me-2"></i>

            Nuevo Curso

        </a>

    </div>

    <!-- Tarjeta principal -->
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
                            id="buscadorCursos"
                            class="form-control"
                            placeholder="Buscar curso..."
                            autocomplete="off">

                    </div>

                </div>

            </div>

            <!-- Tabla -->
            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th style="width:80px;">
                                ID
                            </th>

                            <th>
                                Nombre
                            </th>

                            <th style="width:150px;">
                                Estado
                            </th>

                            <th class="text-center" style="width:180px;">
                                Acciones
                            </th>

                        </tr>

                    </thead>

                    <tbody id="tablaCursos">

                        <?php if (empty($cursos)): ?>

                            <tr>

                                <td colspan="4" class="text-center py-5">

                                    <i class="bi bi-journal-x display-5 text-secondary"></i>

                                    <p class="mt-3 mb-0 text-muted">

                                        No existen cursos registrados.

                                    </p>

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($cursos as $curso): ?>

                                <tr data-busqueda="<?= htmlspecialchars(mb_strtolower($curso->nombre)) ?>">

                                    <td>

                                        <?= $curso->id_curso ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars($curso->nombre) ?>

                                    </td>

                                    <td>

                                        <?php if ($curso->activo): ?>

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

                                        <button
                                            class="btn btn-sm btn-outline-primary me-1"
                                            disabled>

                                            <i class="bi bi-pencil-square"></i>

                                        </button>

                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            disabled>

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

                <p id="sinResultadosCursos" class="text-center text-muted py-4 d-none">

                    <i class="bi bi-search"></i>
                    No se encontraron cursos que coincidan con la búsqueda.

                </p>

                <nav>
                    <ul id="paginacionCursos" class="pagination justify-content-center mt-3"></ul>
                </nav>

            </div>

        </div>

    </div>

</div>

<script src="<?= \Core\Asset::url('/assets/js/tabla-busqueda.js') ?>"></script>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const POR_PAGINA = 7;

    const paginacion = document.getElementById('paginacionCursos');

    let paginaActual = 1;

    const tabla = inicializarBusquedaTabla({
        buscadorId: 'buscadorCursos',
        filasSelector: '#tablaCursos tr[data-busqueda]',
        sinResultadosId: 'sinResultadosCursos',
        alBuscar: () => {
            paginaActual = 1;
            renderizar();
        }
    });

    function crearItemPaginacion(etiqueta, pagina, deshabilitado, activo) {

        const li = document.createElement('li');
        li.className = 'page-item'
            + (deshabilitado ? ' disabled' : '')
            + (activo ? ' active' : '');

        const enlace = document.createElement('a');
        enlace.className = 'page-link';
        enlace.href = '#';
        enlace.textContent = etiqueta;

        enlace.addEventListener('click', (evento) => {

            evento.preventDefault();

            if (!deshabilitado) {
                paginaActual = pagina;
                renderizar();
            }

        });

        li.appendChild(enlace);

        return li;

    }

    function renderizarPaginacion(totalPaginas) {

        if (!paginacion) {
            return;
        }

        paginacion.innerHTML = '';

        if (totalPaginas <= 1) {
            return;
        }

        paginacion.appendChild(
            crearItemPaginacion('Anterior', paginaActual - 1, paginaActual === 1, false)
        );

        for (let pagina = 1; pagina <= totalPaginas; pagina++) {

            paginacion.appendChild(
                crearItemPaginacion(String(pagina), pagina, false, pagina === paginaActual)
            );

        }

        paginacion.appendChild(
            crearItemPaginacion('Siguiente', paginaActual + 1, paginaActual === totalPaginas, false)
        );

    }

    function renderizar() {

        const coincidentes = tabla.obtenerCoincidentes();
        const totalPaginas = Math.max(1, Math.ceil(coincidentes.length / POR_PAGINA));

        if (paginaActual > totalPaginas) {
            paginaActual = totalPaginas;
        }

        const inicio = (paginaActual - 1) * POR_PAGINA;

        tabla.actualizarVisibilidad(coincidentes.slice(inicio, inicio + POR_PAGINA));

        renderizarPaginacion(totalPaginas);

    }

    renderizar();

});

</script>