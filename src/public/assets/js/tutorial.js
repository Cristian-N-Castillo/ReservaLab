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

    const driverObj = window.driver.js.driver({
        showProgress: true,
        allowClose: true,
        overlayOpacity: 0.6,
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        progressText: '{{current}} de {{total}}',
        onDestroyed: marcarVisto,
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
