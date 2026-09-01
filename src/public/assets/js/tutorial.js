document.addEventListener('DOMContentLoaded', () => {

    /*
     * Este script solo actúa si la vista definió window.tutorialReservaLab
     * (ver dashboard/docente.php). Si no existe, no hacemos nada.
     */
    const config = window.tutorialReservaLab;

    if (!config || !config.mostrar || typeof driver === 'undefined') {
        return;
    }

    const marcarVisto = () => {

        fetch('/tutorial/visto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: '_token=' + encodeURIComponent(config.token)
        }).catch(() => {
            /*
             * Si falla el aviso al servidor, no interrumpimos al
             * docente: en el peor caso, el tutorial se le mostrará
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
        steps: [
            {
                popover: {
                    title: '👋 ¡Bienvenido a ReservaLab!',
                    description: 'Te mostramos rápidamente cómo funciona el sistema antes de empezar. Puedes cerrar este recorrido en cualquier momento.'
                }
            },
            {
                element: '#tour-nav-dashboard',
                popover: {
                    title: 'Dashboard',
                    description: 'Aquí siempre puedes volver a tu panel principal, con el resumen de tus reservas del mes.',
                    side: 'right',
                    align: 'start'
                }
            },
            {
                element: '#tour-nav-laboratorios',
                popover: {
                    title: 'Laboratorios',
                    description: 'Aquí ves los laboratorios disponibles, su capacidad, y puedes reservarlos directamente desde el botón "Reservar".',
                    side: 'right',
                    align: 'start'
                }
            },
            {
                element: '#tour-nav-reservas',
                popover: {
                    title: 'Reservas',
                    description: 'Aquí puedes crear una nueva reserva: eliges fecha, laboratorio, curso y hasta 3 bloques horarios.',
                    side: 'right',
                    align: 'start'
                }
            },
            {
                element: '#tour-nueva-reserva',
                popover: {
                    title: 'Acceso rápido',
                    description: 'También puedes crear una reserva directamente desde este botón, sin pasar por el menú.',
                    side: 'bottom',
                    align: 'end'
                }
            },
            {
                element: '#tour-stats',
                popover: {
                    title: 'Tu resumen',
                    description: 'Aquí ves cuántas reservas tienes este mes, cuáles están próximas y cuáles fueron canceladas.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#tour-navbar-perfil',
                popover: {
                    title: 'Tu perfil',
                    description: 'Desde aquí puedes cambiar tu avatar, tu contraseña, o cerrar sesión.',
                    side: 'bottom',
                    align: 'end'
                }
            },
            {
                popover: {
                    title: '✅ ¡Listo!',
                    description: 'Ya puedes empezar a usar ReservaLab. Recuerda que confirmar o cancelar tus reservas se hace desde el enlace que te llega por correo.'
                }
            }
        ]
    });

    driverObj.drive();

});
