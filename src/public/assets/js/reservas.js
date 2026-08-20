document.addEventListener('DOMContentLoaded', () => {

    /*
     * =========================================================
     * ELEMENTOS DEL FORMULARIO
     * =========================================================
     */

    const formulario = document.getElementById('formReserva');

    const inputFecha = document.getElementById('fecha');

    const selectLaboratorio = document.getElementById(
        'id_laboratorio'
    );

    const inputHorario = document.getElementById('id_horario');

    const bloqueSeleccionado = document.getElementById(
        'bloqueSeleccionado'
    );

    const botonReservar = document.getElementById(
        'btnReservar'
    );

    const botonesBloque = document.querySelectorAll(
        '.btn-seleccionar-bloque'
    );


    /*
     * Si no estamos en la pantalla de reservas,
     * no ejecutamos el resto del código.
     */
    if (
        !formulario ||
        !inputFecha ||
        !selectLaboratorio ||
        !inputHorario ||
        !bloqueSeleccionado ||
        !botonReservar
    ) {
        return;
    }


    /*
     * =========================================================
     * SELECTOR DE FECHA (FLATPICKR)
     * =========================================================
     *
     * Reemplaza el <input type="date"> nativo para poder mostrar
     * sábado y domingo deshabilitados (en gris) directamente en
     * el calendario, igual que las fechas pasadas.
     */

    const MAX_DIAS_ANTICIPACION = 21;

    if (typeof flatpickr !== 'undefined') {

        flatpickr(inputFecha, {
            locale: 'es',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            minDate: 'today',
            maxDate: new Date().fp_incr(MAX_DIAS_ANTICIPACION),
            disable: [
                (fecha) => fecha.getDay() === 0 || fecha.getDay() === 6
            ],
        });

    }


    /*
     * =========================================================
     * ACTUALIZAR AGENDA
     * =========================================================
     *
     * Al cambiar fecha o laboratorio recargamos la pantalla
     * enviando ambos valores mediante GET.
     *
     * Ejemplo:
     *
     * /reservas?fecha=2026-08-11&id_laboratorio=2
     */

    const esFinDeSemana = (fechaTexto) => {

        const [anio, mes, dia] = fechaTexto.split('-').map(Number);

        const diaSemana = new Date(anio, mes - 1, dia).getDay();

        return diaSemana === 0 || diaSemana === 6;
    };

    const actualizarAgenda = () => {

        const fecha = inputFecha.value;

        const idLaboratorio = selectLaboratorio.value;

        if (!fecha || !idLaboratorio) {
            return;
        }

        if (esFinDeSemana(fecha)) {

            alert(
                'No se pueden realizar reservas los días sábado ni domingo. ' +
                'Por favor seleccione otra fecha.'
            );

            inputFecha.value = '';

            return;
        }

        const parametros = new URLSearchParams();

        parametros.set(
            'fecha',
            fecha
        );

        parametros.set(
            'id_laboratorio',
            idLaboratorio
        );

        window.location.href =
            '/reservas?' + parametros.toString();
    };


    /*
     * Cambio de fecha.
     */
    inputFecha.addEventListener(
        'change',
        actualizarAgenda
    );


    /*
     * Cambio de laboratorio.
     */
    selectLaboratorio.addEventListener(
        'change',
        actualizarAgenda
    );


    /*
     * =========================================================
     * SELECCIÓN DE BLOQUE
     * =========================================================
     */

    botonesBloque.forEach((boton) => {

        boton.addEventListener('click', () => {

            const id = boton.dataset.id;

            const nombre = boton.dataset.nombre;

            const inicio = boton.dataset.inicio;

            const fin = boton.dataset.fin;


            /*
             * Guardamos el ID real del horario.
             */
            inputHorario.value = id;


            /*
             * Restauramos todos los botones disponibles.
             */
            botonesBloque.forEach((otroBoton) => {

                otroBoton.classList.remove(
                    'btn-success'
                );

                otroBoton.classList.add(
                    'btn-outline-success'
                );

                otroBoton.innerHTML = `
                    <i class="bi bi-check2-circle me-2"></i>
                    Seleccionar
                `;

            });


            /*
             * Marcamos el bloque seleccionado.
             */
            boton.classList.remove(
                'btn-outline-success'
            );

            boton.classList.add(
                'btn-success'
            );

            boton.innerHTML = `
                <i class="bi bi-check-circle-fill me-2"></i>
                Seleccionado
            `;


            /*
             * Mostramos el bloque seleccionado.
             */
            bloqueSeleccionado.classList.remove(
                'alert-secondary',
                'alert-danger'
            );

            bloqueSeleccionado.classList.add(
                'alert-success'
            );

            bloqueSeleccionado.innerHTML = `
                <i class="bi bi-check-circle-fill me-2"></i>

                <strong>
                    ${nombre}
                </strong>

                &nbsp;

                ${inicio} - ${fin}
            `;


            /*
             * Habilitamos el botón de reserva.
             */
            botonReservar.disabled = false;

        });

    });


    /*
     * =========================================================
     * VALIDACIÓN ANTES DE ENVIAR
     * =========================================================
     */

    formulario.addEventListener(
        'submit',
        (event) => {

            if (!inputHorario.value) {

                event.preventDefault();

                bloqueSeleccionado.classList.remove(
                    'alert-secondary',
                    'alert-success'
                );

                bloqueSeleccionado.classList.add(
                    'alert-danger'
                );

                bloqueSeleccionado.innerHTML = `
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Debe seleccionar un bloque horario.
                `;

                botonReservar.disabled = true;
            }

        }
    );

});