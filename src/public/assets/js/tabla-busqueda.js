/**
 * Filtro de búsqueda para tablas con filas marcadas con
 * data-busqueda="<texto en minúsculas>". Cubre el caso simple
 * (ocultar/mostrar filas al escribir) y permite delegar el
 * renderizado a través de `alBuscar` cuando la página necesita algo
 * más, como paginación (ver cursos/index.php).
 */
function inicializarBusquedaTabla(opciones) {

    const buscador = document.getElementById(opciones.buscadorId);
    const filas = Array.from(document.querySelectorAll(opciones.filasSelector));
    const sinResultados = opciones.sinResultadosId
        ? document.getElementById(opciones.sinResultadosId)
        : null;

    function obtenerCoincidentes() {

        const texto = buscador ? buscador.value.trim().toLowerCase() : '';

        return filas.filter(fila => fila.dataset.busqueda.includes(texto));

    }

    function actualizarVisibilidad(visibles) {

        const visiblesSet = new Set(visibles);

        filas.forEach(fila => {
            fila.classList.toggle('d-none', !visiblesSet.has(fila));
        });

        if (sinResultados) {
            sinResultados.classList.toggle('d-none', visibles.length > 0 || filas.length === 0);
        }

    }

    function manejarBusqueda() {

        const coincidentes = obtenerCoincidentes();

        if (typeof opciones.alBuscar === 'function') {
            opciones.alBuscar(coincidentes);
        } else {
            actualizarVisibilidad(coincidentes);
        }

    }

    if (buscador) {
        buscador.addEventListener('input', manejarBusqueda);
    }

    return { filas, obtenerCoincidentes, actualizarVisibilidad };

}
