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
     * reposicionándose junto a él en cada paso. Un solo elemento
     * compartido por todos los tours del sistema.
     */
    const mascota = document.createElement('div');
    mascota.className = 'tour-mascot';
    mascota.textContent = '🤖';
    document.body.appendChild(mascota);

    const posicionarMascota = (popoverEl) => {

        if (!popoverEl) {
            return;
        }

        const margen = 14;
        const rect = popoverEl.getBoundingClientRect();
        const anchoMascota = mascota.offsetWidth || 64;
        const altoMascota = mascota.offsetHeight || 64;

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
    };

    const ocultarMascota = () => {
        mascota.style.display = 'none';
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
        },
        onDestroyed: () => {
            ocultarMascota();
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
