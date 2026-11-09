let currentToggleFormId = null;


// ==========================================
// FUNCIONES DEL MODAL DE CONFIRMACIÓN (ACTIVAR/DESACTIVAR)
// ==========================================
function openConfirmModal(itemId, action, itemName) {
    currentToggleFormId = 'toggle-form-' + itemId;

    const modal = document.getElementById('confirmToggleModal');
    const title = document.getElementById('confirmModalTitle');
    const message = document.getElementById('confirmModalMessage');
    const confirmBtn = document.getElementById('btnConfirmToggle');

    // Restaurar estado visual del botón
    confirmBtn.disabled = false;
    confirmBtn.style.opacity = '1';
    confirmBtn.style.cursor = 'pointer';

    if (action === 'desactivar') {
        title.innerText = 'Desactivar Kit';
        message.innerHTML = `¿Estás seguro que deseas desactivar el kit <strong>"${itemName}"</strong>?<br><br><span style="font-size: 0.85rem; color: #6b7280;">Este kit dejará de estar visible para ventas.</span>`;

        confirmBtn.style.backgroundColor = '#dc2626';
        confirmBtn.style.borderColor = '#dc2626';
        confirmBtn.innerHTML = '<i class="fa-solid fa-power-off"></i> Sí, desactivar';
    } else {
        title.innerText = 'Activar Kit';
        message.innerHTML = `¿Estás seguro que deseas activar el kit <strong>"${itemName}"</strong>?<br><br><span style="font-size: 0.85rem; color: #6b7280;">Este kit volverá a estar disponible públicamente en la plataforma.</span>`;

        confirmBtn.style.backgroundColor = 'var(--color-primary)';
        confirmBtn.style.borderColor = 'var(--color-primary)';
        confirmBtn.innerHTML = '<i class="fa-solid fa-check"></i> Sí, activar';
    }

    modal.style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirmToggleModal').style.display = 'none';
    currentToggleFormId = null;
}

function submitToggleForm() {
    if (currentToggleFormId) {
        // Aplicar efecto de carga al botón de confirmar
        const confirmBtn = document.getElementById('btnConfirmToggle');
        confirmBtn.disabled = true;
        confirmBtn.style.opacity = '0.7';
        confirmBtn.style.cursor = 'not-allowed';
        confirmBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Procesando...';

        document.getElementById(currentToggleFormId).submit();
    }
}

// ==========================================
// FUNCIONES DEL MODAL PRINCIPAL (NUEVO/EDITAR)
// ==========================================
function openKitModal(btn = null) {
    const modal = document.getElementById('kitModal');
    const form = document.getElementById('kitForm');
    const formMethod = document.getElementById('formMethod');
    const title = document.getElementById('modalTitle');
    const submitBtn = form.querySelector('button[type="submit"]');

    if (btn) {
        title.innerText = 'Editar Kit';
        formMethod.value = 'PUT';
        form.action = `/admin/catalogo/kits/${btn.dataset.id}`;
        submitBtn.innerText = 'Editar Kit';

        document.getElementById('kitId').value = btn.dataset.id;
        document.getElementById('sku').value = btn.dataset.sku;
        document.getElementById('name').value = btn.dataset.name;
        document.getElementById('sector').value = btn.dataset.sector;
        document.getElementById('price').value = btn.dataset.price;
        document.getElementById('description').value = btn.dataset.desc;

        // Restaurar el estado del toggle
        document.getElementById('is_active').checked = (btn.dataset.active === '1');
    } else {
        title.innerText = 'Nuevo Kit';
        formMethod.value = 'POST';
        form.action = `/admin/catalogo/kits`;
        submitBtn.innerText = 'Guardar Kit';
        form.reset();
        document.getElementById('kitId').value = '';

        // Asegurar que por defecto esté activo al crear uno nuevo
        document.getElementById('is_active').checked = true;
    }
    modal.style.display = 'flex';
}

function closeKitModal() {
    document.getElementById('kitModal').style.display = 'none';
    document.getElementById('kitForm').reset();
}
