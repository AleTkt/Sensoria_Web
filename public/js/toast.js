// public/js/toast.js

document.addEventListener('DOMContentLoaded', function() {
    const toastElements = document.querySelectorAll('.toast-brutal');

    toastElements.forEach(function(toast) {
        // 1. Añadir un pequeño retraso escalonado para que aparezcan en secuencia si hay varios
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        // 2. Configurar el auto-ocultamiento a los 5 segundos
        let hideTimeout = setTimeout(() => {
            closeToast(toast);
        }, 5000);

        // 3. Pausar el temporizador si el usuario pasa el ratón por encima
        toast.addEventListener('mouseenter', () => clearTimeout(hideTimeout));
        toast.addEventListener('mouseleave', () => {
            hideTimeout = setTimeout(() => closeToast(toast), 3000); // 3 segundos extra
        });

        // 4. Funcionalidad del botón de cierre manual
        const closeBtn = toast.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                clearTimeout(hideTimeout);
                closeToast(toast);
            });
        }
    });

    function closeToast(toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');

        // Esperar a que termine la animación para eliminar el nodo del DOM
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 400); // Coincide con la duración de la transición CSS
    }
});
