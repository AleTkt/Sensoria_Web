// public/js/loading-buttons.js

document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos todos los formularios que queremos proteger
    const forms = document.querySelectorAll('.form-with-loading');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Buscamos el botón de envío dentro de este formulario específico
            const submitBtn = this.querySelector('.btn-submit-loading');

            if (submitBtn) {
                // Prevenimos múltiples clics deshabilitando el botón
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.7';
                submitBtn.style.cursor = 'not-allowed';

                // Guardamos el contenido original por si queremos restaurarlo (opcional,
                // ya que normalmente la página recargará tras el POST)
                if (!submitBtn.dataset.originalContent) {
                    submitBtn.dataset.originalContent = submitBtn.innerHTML;
                }

                // Cambiamos el texto y agregamos el icono de spinner de FontAwesome
                submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Procesando...';
            }
        });
    });
});
