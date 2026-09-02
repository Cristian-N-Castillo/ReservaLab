document.addEventListener('DOMContentLoaded', () => {

    /*
     * Motor genérico de tours guiados: cada vista define su propio
     * window.tourConfig = { modulo, mostrar, token, steps } antes
     * de incluir este script. Si no existe, no hacemos nada.
     */
    const config = window.tourConfig;

    if (!config || typeof driver === 'undefined') {
        return;
    }

    const marcarVisto = () => {

        fetch('/tutorial/visto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: '_token=' + encodeURIComponent(config.token)
                + '&modulo=' + encodeURIComponent(config.modulo)
        }).catch(() => {
            /*
             * Si falla el aviso al servidor, no interrumpimos al
             * usuario: en el peor caso, el tutorial se le mostrará
             * de nuevo la próxima vez.
             */
        });
    };

    /*
     * Mascota (robot) que acompaña al cuadro de diálogo del tour,
     * reposicionándose junto a él en cada paso y saludando con el
     * brazo cada vez que salta al siguiente. Un solo elemento SVG
     * compartido por todos los tours del sistema.
     */
    const mascota = document.createElement('div');
    mascota.className = 'tour-mascot';
    mascota.innerHTML = `
        <svg class="tm-svg" viewBox="0 0 80 110" xmlns="http://www.w3.org/2000/svg">
            <ellipse class="tm-sombra" cx="40" cy="103" rx="18" ry="5"></ellipse>
            <rect class="tm-pierna" x="27" y="80" width="9" height="20" rx="4"></rect>
            <rect class="tm-pierna" x="44" y="80" width="9" height="20" rx="4"></rect>
            <rect class="tm-cuerpo" x="17" y="42" width="46" height="42" rx="11"></rect>
            <circle class="tm-pantalla" cx="40" cy="63" r="8"></circle>
            <rect class="tm-brazo" x="5" y="46" width="10" height="27" rx="5"></rect>
            <g class="tm-brazo-der-grupo">
                <rect class="tm-brazo" x="65" y="46" width="10" height="27" rx="5"></rect>
            </g>
            <g class="tm-antena-grupo">
                <line class="tm-antena" x1="40" y1="10" x2="40" y2="2"></line>
                <circle class="tm-antena-bola" cx="40" cy="2" r="3"></circle>
            </g>
            <rect class="tm-cabeza" x="13" y="10" width="54" height="36" rx="14"></rect>
            <circle class="tm-ojo" cx="28" cy="28" r="5"></circle>
            <circle class="tm-ojo" cx="52" cy="28" r="5"></circle>
            <rect class="tm-boca" x="30" y="38" width="20" height="3" rx="1.5"></rect>
        </svg>
    `;
    document.body.appendChild(mascota);

    const saludarConElBrazo = () => {
        const brazo = mascota.querySelector('.tm-brazo-der-grupo');

        if (!brazo) {
            return;
        }

        brazo.classList.remove('tm-saludando');
        void brazo.offsetWidth;
        brazo.classList.add('tm-saludando');
    };

    const posicionarMascota = (popoverEl) => {

        if (!popoverEl) {
            return;
        }

        const margen = 14;
        const rect = popoverEl.getBoundingClientRect();
        const anchoMascota = mascota.offsetWidth || 68;
        const altoMascota = mascota.offsetHeight || 94;

        const espacioDerecha = window.innerWidth - rect.right;
        const espacioIzquierda = rect.left;

        let left;

        if (espacioDerecha >= anchoMascota + margen) {
            left = rect.right + margen;
        } else if (espacioIzquierda >= anchoMascota + margen) {
            left = rect.left - anchoMascota - margen;
        } else {
            left = rect.left + (rect.width / 2) - (anchoMascota / 2);
        }

        let top = espacioDerecha >= anchoMascota + margen
            || espacioIzquierda >= anchoMascota + margen
            ? rect.top + (rect.height / 2) - (altoMascota / 2)
            : rect.bottom + margen;

        left = Math.max(
            margen,
            Math.min(left, window.innerWidth - anchoMascota - margen)
        );

        top = Math.max(
            margen,
            Math.min(top, window.innerHeight - altoMascota - margen)
        );

        mascota.style.left = left + 'px';
        mascota.style.top = top + 'px';
        mascota.style.display = 'flex';

        saludarConElBrazo();
    };

    const ocultarMascota = () => {
        mascota.style.display = 'none';
    };

    /*
     * Voz que lee cada paso en voz alta (Web Speech API, nativa del
     * navegador, sin dependencias externas). Se elige una voz en
     * español apenas el navegador las tenga disponibles: en varios
     * navegadores la lista llega de forma asíncrona.
     */
    const vozDisponible = 'speechSynthesis' in window;
    let vozEspanol = null;

    const elegirVoz = () => {

        if (!vozDisponible) {
            return;
        }

        const voces = window.speechSynthesis.getVoices();

        vozEspanol = voces.find((v) => v.lang === 'es-CL')
            || voces.find((v) => v.lang === 'es-ES')
            || voces.find((v) => v.lang === 'es-MX')
            || voces.find((v) => v.lang && v.lang.startsWith('es'))
            || null;
    };

    if (vozDisponible) {
        elegirVoz();
        window.speechSynthesis.onvoiceschanged = elegirVoz;
    }

    const limpiarParaVoz = (texto) => texto
        .replace(/\p{Extended_Pictographic}/gu, '')
        .replace(/\s+/g, ' ')
        .trim();

    const leerPaso = (popover) => {

        if (!vozDisponible) {
            return;
        }

        window.speechSynthesis.cancel();

        const titulo = popover.title ? popover.title.innerText : '';
        const descripcion = popover.description ? popover.description.innerText : '';
        const texto = limpiarParaVoz(titulo + '. ' + descripcion);

        if (!texto) {
            return;
        }

        const utterance = new SpeechSynthesisUtterance(texto);

        utterance.lang = vozEspanol ? vozEspanol.lang : 'es-CL';
        if (vozEspanol) {
            utterance.voice = vozEspanol;
        }
        utterance.rate = 0.95;
        utterance.pitch = 0.8;
        utterance.volume = 1;

        window.speechSynthesis.speak(utterance);
    };

    const driverObj = window.driver.js.driver({
        showProgress: true,
        allowClose: true,
        overlayOpacity: 0.6,
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        progressText: '{{current}} de {{total}}',
        onPopoverRender: (popover) => {
            requestAnimationFrame(() => posicionarMascota(popover.wrapper));
            leerPaso(popover);
        },
        onDestroyed: () => {
            ocultarMascota();
            if (vozDisponible) {
                window.speechSynthesis.cancel();
            }
            marcarVisto();
        },
        steps: config.steps
    });

    /*
     * Se expone globalmente para que un botón "Ver guía" en la vista
     * pueda relanzar el mismo tour en cualquier momento, no solo la
     * primera vez.
     */
    window.iniciarTourGuiado = () => driverObj.drive();

    if (config.mostrar) {
        driverObj.drive();
    }

});
