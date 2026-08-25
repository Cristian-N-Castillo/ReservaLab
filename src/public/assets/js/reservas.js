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

    const idsHorarioContainer = document.getElementById(
        'idsHorarioContainer'
    );

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
        !idsHorarioContainer ||
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
     * SELECCIÓN DE BLOQUES (hasta 3, en cualquier orden)
     * =========================================================
     */

    const MAX_BLOQUES = 3;

    const idsSeleccionados = new Set();

    const actualizarBotonBloque = (boton, seleccionado) => {

        boton.classList.toggle('btn-success', seleccionado);
        boton.classList.toggle('btn-outline-success', !seleccionado);

        boton.innerHTML = seleccionado
            ? `<i class="bi bi-check-circle-fill me-2"></i> Seleccionado`
            : `<i class="bi bi-check2-circle me-2"></i> Seleccionar`;
    };

    const actualizarResumenBloques = () => {

        /*
         * Reconstruimos los inputs ocultos y el resumen siguiendo
         * el orden cronológico de los bloques (orden del DOM), sin
         * importar el orden en que el docente los haya clickeado.
         */
        const bloquesSeleccionados = Array.from(botonesBloque).filter(
            (boton) => idsSeleccionados.has(boton.dataset.id)
        );

        idsHorarioContainer.innerHTML = '';

        bloquesSeleccionados.forEach((boton) => {

            const input = document.createElement('input');

            input.type = 'hidden';
            input.name = 'id_horario[]';
            input.value = boton.dataset.id;

            idsHorarioContainer.appendChild(input);
        });

        if (bloquesSeleccionados.length === 0) {

            bloqueSeleccionado.classList.remove(
                'alert-success',
                'alert-danger'
            );

            bloqueSeleccionado.classList.add('alert-secondary');

            bloqueSeleccionado.innerHTML = `
                <i class="bi bi-clock me-2"></i>
                Aún no ha seleccionado ningún bloque horario.
            `;

            botonReservar.disabled = true;

            return;
        }

        bloqueSeleccionado.classList.remove(
            'alert-secondary',
            'alert-danger'
        );

        bloqueSeleccionado.classList.add('alert-success');

        bloqueSeleccionado.innerHTML = bloquesSeleccionados
            .map((boton) => `
                <div>
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>${boton.dataset.nombre}</strong>
                    &nbsp;
                    ${boton.dataset.inicio} - ${boton.dataset.fin}
                </div>
            `)
            .join('');

        botonReservar.disabled = false;
    };

    botonesBloque.forEach((boton) => {

        boton.addEventListener('click', () => {

            const id = boton.dataset.id;

            if (idsSeleccionados.has(id)) {

                idsSeleccionados.delete(id);

                actualizarBotonBloque(boton, false);

                actualizarResumenBloques();

                return;
            }

            if (idsSeleccionados.size >= MAX_BLOQUES) {

                alert(
                    `Solo puede seleccionar hasta ${MAX_BLOQUES} bloques ` +
                    'por reserva.'
                );

                return;
            }

            idsSeleccionados.add(id);

            actualizarBotonBloque(boton, true);

            actualizarResumenBloques();
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

            if (idsSeleccionados.size === 0) {

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
                    Debe seleccionar al menos un bloque horario.
                `;

                botonReservar.disabled = true;
            }

        }
    );

});