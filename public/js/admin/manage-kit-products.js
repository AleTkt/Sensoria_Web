let currentKitId = null;
let currentKitName = '';
let availableProducts = [];
let selectedCategory = 'all';
let pendingProductIds = [];
let pendingQuantity = 1;

/**
 * Maneja el estado visual de carga de un botón.
 * Deshabilita el botón, reduce su opacidad y muestra un spinner mientras se procesa una acción.
 * @param {HTMLElement} btn - El elemento del botón a modificar.
 * @param {boolean} isLoading - Indica si el botón debe entrar o salir del estado de carga.
 * @param {string|null} originalContent - El contenido original del botón para restaurarlo después.
 */
function setButtonLoading(btn, isLoading, originalContent = null) {
    if (!btn) return;

    if (isLoading) {
        if (!originalContent) {
            btn.dataset.originalContent = btn.innerHTML;
        }
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btn.style.cursor = 'not-allowed';
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
    } else {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        if (originalContent || btn.dataset.originalContent) {
            btn.innerHTML = originalContent || btn.dataset.originalContent;
        }
    }
}

/**
 * Inicializa y abre el modal de gestión de productos para un kit específico.
 * Configura las variables de estado, actualiza el título del modal y carga los datos necesarios.
 * @param {number} kitId - El ID del kit que se va a gestionar.
 * @param {string} kitName - El nombre del kit para mostrar en la interfaz.
 */
async function openManageKitModal(kitId, kitName) {
    currentKitId = kitId;
    currentKitName = kitName;

    const modal = document.getElementById('manageKitModal');
    const kitNameDisplay = document.getElementById('kitNameDisplay');
    const manageModalTitle = document.getElementById('manageModalTitle');

    if (!modal) {
        alert('Error: El modal no está cargado en la página');
        return;
    }

    if (kitNameDisplay) {
        kitNameDisplay.textContent = kitName;
    }

    if (manageModalTitle) {
        manageModalTitle.innerHTML = `Gestionar Productos: Kit <span style="color: var(--color-primary);">${kitName}</span>`;
    }

    modal.style.display = 'flex';

    await loadProductsInKit();
    await loadAvailableProducts();
}

/**
 * Cierra el modal de gestión y resetea las variables de estado a sus valores por defecto.
 */
function closeManageKitModal() {
    const modal = document.getElementById('manageKitModal');
    if (modal) {
        modal.style.display = 'none';
    }
    currentKitId = null;
    currentKitName = '';
    selectedCategory = 'all';
}

/**
 * Obtiene los productos que ya están asignados al kit actual desde el servidor.
 * Renderiza la lista de productos con sus controles de cantidad (+/-) y el botón de eliminar.
 */
async function loadProductsInKit() {
    const listContainer = document.getElementById('productsInKitList');
    if (!listContainer) return;

    listContainer.innerHTML = '<p style="color: #6b7280; text-align: center; padding: 2rem;"><i class="fa-solid fa-circle-notch fa-spin"></i> Cargando...</p>';

    try {
        const url = `/admin/catalogo/kits/${currentKitId}/productos-data`;
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (!data.products || data.products.length === 0) {
            listContainer.innerHTML = '<p style="color: #6b7280; text-align: center; padding: 2rem;">No hay productos en este kit aún.</p>';
            return;
        }

        let html = '';
        data.products.forEach(product => {
            const quantity = product.pivot.quantity;
            html += `
                <div class="kit-product-item">
                    <div class="kit-product-info">
                        <div class="kit-product-name">${product.name}</div>
                        <div class="kit-product-sku">SKU: ${product.sku} | $${parseFloat(product.price).toFixed(2)} c/u</div>
                    </div>
                    <div class="kit-product-actions">
                        <div class="quantity-control">
                            <button type="button"
                                    class="qty-btn btn-qty-loading"
                                    data-product-id="${product.id}"
                                    data-action="decrease"
                                    ${quantity <= 1 ? 'disabled' : ''}>
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number"
                                   class="qty-input"
                                   value="${quantity}"
                                   min="1"
                                   onchange="updateProductQuantity(${product.id}, this.value)"
                                   onclick="this.select()">
                            <button type="button"
                                    class="qty-btn btn-qty-loading"
                                    data-product-id="${product.id}"
                                    data-action="increase">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <button type="button" class="btn-remove-product" onclick="removeProductFromKit(${product.id})" title="Eliminar del kit">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        listContainer.innerHTML = html;

        document.querySelectorAll('.btn-qty-loading').forEach(btn => {
            btn.addEventListener('click', handleQuantityButtonClick);
        });

    } catch (error) {
        listContainer.innerHTML = `
            <div style="color: #dc2626; text-align: center; padding: 2rem;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Error al cargar los productos.</p>
                <p style="font-size: 0.8rem;">${error.message}</p>
            </div>
        `;
    }
}

/**
 * Maneja los clics en los botones de aumentar o disminuir cantidad.
 * Calcula la nueva cantidad, aplica el estado de carga al botón y envía la actualización al servidor.
 * @param {Event} e - El evento de clic del botón.
 */
async function handleQuantityButtonClick(e) {
    const btn = e.currentTarget;
    const productId = parseInt(btn.dataset.productId);
    const action = btn.dataset.action;

    const input = btn.parentElement.querySelector('.qty-input');
    let currentQty = parseInt(input.value);

    let newQuantity = action === 'increase' ? currentQty + 1 : currentQty - 1;

    if (newQuantity < 1) {
        alert('La cantidad mínima es 1.');
        return;
    }

    setButtonLoading(btn, true);

    try {
        const url = `/admin/catalogo/kits/${currentKitId}/productos/${productId}`;

        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQuantity })
        });

        const data = await response.json();

        if (data.success) {
            await loadProductsInKit();
        } else {
            alert(data.message || 'Error al actualizar.');
            setButtonLoading(btn, false, btn.dataset.originalContent);
        }
    } catch (error) {
        alert('Ocurrió un error al actualizar.');
        setButtonLoading(btn, false, btn.dataset.originalContent);
    }
}

/**
 * Obtiene la lista completa de productos de hardware disponibles desde el servidor.
 * Una vez cargados, dispara la carga de categorías y el renderizado de la lista.
 */
async function loadAvailableProducts() {
    const listContainer = document.getElementById('availableProductsList');
    if (!listContainer) return;

    listContainer.innerHTML = '<p style="color: #6b7280; text-align: center; padding: 1rem;"><i class="fa-solid fa-circle-notch fa-spin"></i> Cargando...</p>';

    try {
        const url = '/admin/Products/hardware/data';
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        availableProducts = data.products || data;

        await loadCategories();
        renderAvailableProducts();
    } catch (error) {
        listContainer.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 1rem;">Error al cargar productos disponibles.</p>';
    }
}

/**
 * Obtiene las categorías únicas de productos desde la base de datos.
 * Genera dinámicamente los botones de filtro en la interfaz, manteniendo siempre el botón "Todos".
 */
async function loadCategories() {
    const categoryContainer = document.getElementById('categoryFilters');
    if (!categoryContainer) return;

    try {
        const response = await fetch('/admin/Products/hardware/categories');
        const data = await response.json();

        const allButton = categoryContainer.querySelector('[data-category="all"]');
        categoryContainer.innerHTML = '';
        if (allButton) {
            categoryContainer.appendChild(allButton);
        }

        if (data.categories && data.categories.length > 0) {
            data.categories.forEach(category => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'category-filter-btn';
                btn.dataset.category = category;
                btn.textContent = category;
                btn.onclick = () => filterProductsByCategory(category);
                categoryContainer.appendChild(btn);
            });
        }
    } catch (error) {
        // Silencioso en caso de error de carga de categorías, usa "Todos" por defecto.
    }
}

/**
 * Actualiza el estado de la categoría seleccionada y vuelve a renderizar la lista de productos disponibles.
 * @param {string} category - La categoría a filtrar ('all' o el nombre de la categoría).
 */
function filterProductsByCategory(category) {
    selectedCategory = category;

    document.querySelectorAll('.category-filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.category === category) {
            btn.classList.add('active');
        }
    });

    renderAvailableProducts();
}

/**
 * Renderiza la lista de productos disponibles en el lado derecho del modal.
 * Aplica el filtro de categoría seleccionado y genera el HTML con checkboxes para cada producto.
 */
function renderAvailableProducts() {
    const listContainer = document.getElementById('availableProductsList');
    if (!listContainer) return;

    let filtered = availableProducts;
    if (selectedCategory !== 'all') {
        filtered = availableProducts.filter(p => p.category === selectedCategory);
    }

    if (filtered.length === 0) {
        listContainer.innerHTML = '<p style="color: #6b7280; text-align: center; padding: 1rem;">No hay productos disponibles en esta categoría.</p>';
        return;
    }

    let html = '';
    filtered.forEach(product => {
        html += `
            <div class="product-checkbox-item">
                <label>
                    <input type="checkbox" name="product_${product.id}" value="${product.id}">
                    <div class="product-info">
                        <div class="product-name">${product.name}</div>
                        <div class="product-sku">${product.category} | $${parseFloat(product.price).toFixed(2)}</div>
                    </div>
                </label>
            </div>
        `;
    });

    listContainer.innerHTML = html;
}

/**
 * Valida la selección de productos y la cantidad ingresada antes de abrir el modal de confirmación.
 * Prepara los datos pendientes (IDs y cantidad) para el envío posterior.
 */
function addProductsToKit() {
    const checkboxes = document.querySelectorAll('#availableProductsList input[type="checkbox"]:checked');
    const quantity = document.getElementById('productQuantity').value;

    if (checkboxes.length === 0) {
        alert('Selecciona al menos un producto.');
        return;
    }

    if (quantity < 1) {
        alert('La cantidad debe ser al menos 1.');
        return;
    }

    pendingProductIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
    pendingQuantity = parseInt(quantity);

    const productNames = Array.from(checkboxes).map(cb => {
        const label = cb.closest('label');
        if (label) {
            const nameEl = label.querySelector('.product-name');
            return nameEl ? nameEl.innerText : 'Producto';
        }
        return 'Producto';
    });

    const confirmMessage = document.getElementById('confirmAddMessage');
    if (confirmMessage) {
        confirmMessage.innerHTML = `¿Estás seguro de agregar <strong>${pendingQuantity}</strong> unidades de <strong>${productNames.join(', ')}</strong> a <strong>${currentKitName}</strong>?`;
    }

    const confirmModal = document.getElementById('confirmAddKitModal');
    if (confirmModal) {
        confirmModal.style.display = 'flex';
    }
}

/**
 * Cierra el modal de confirmación de agregado de productos.
 */
function closeConfirmAddModal() {
    const modal = document.getElementById('confirmAddKitModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Ejecuta la petición POST al servidor para agregar los productos seleccionados al kit.
 * Maneja el estado de carga del botón, procesa la respuesta y actualiza la interfaz en caso de éxito.
 */
async function confirmAddProducts() {
    const confirmBtn = document.querySelector('#confirmAddKitModal .btn-sensoria');

    if (!confirmBtn) return;

    setButtonLoading(confirmBtn, true);

    try {
        const url = `/admin/catalogo/kits/${currentKitId}/productos/batch`;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                products: pendingProductIds,
                quantity: pendingQuantity
            })
        });

        const data = await response.json();

        if (data.success) {
            closeConfirmAddModal();
            setButtonLoading(confirmBtn, false);
            await loadProductsInKit();
            await loadAvailableProducts();

            document.querySelectorAll('#availableProductsList input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.getElementById('productQuantity').value = 1;

        } else {
            alert(data.message || 'Error al agregar productos.');
            setButtonLoading(confirmBtn, false);
        }
    } catch (error) {
        alert('Ocurrió un error al procesar la solicitud.');
        setButtonLoading(confirmBtn, false);
    }
}

/**
 * Maneja la actualización directa de cantidad desde el campo de entrada numérico (onchange).
 * Valida el valor y envía una petición PATCH al servidor.
 * @param {number} productId - El ID del producto a actualizar.
 * @param {number|string} newQuantity - La nueva cantidad ingresada.
 */
async function updateProductQuantity(productId, newQuantity) {
    newQuantity = parseInt(newQuantity);
    if (newQuantity < 1) {
        alert('La cantidad mínima es 1.');
        await loadProductsInKit();
        return;
    }

    try {
        const url = `/admin/catalogo/kits/${currentKitId}/productos/${productId}`;

        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQuantity })
        });

        const data = await response.json();

        if (data.success) {
            await loadProductsInKit();
        } else {
            alert(data.message || 'Error al actualizar.');
            await loadProductsInKit();
        }
    } catch (error) {
        alert('Ocurrió un error al actualizar.');
        await loadProductsInKit();
    }
}

/**
 * Elimina un producto del kit actual tras la confirmación del usuario.
 * Envía una petición DELETE al servidor y recarga la lista de productos del kit.
 * @param {number} productId - El ID del producto a eliminar.
 */
async function removeProductFromKit(productId) {
    if (!confirm('¿Estás seguro de eliminar este producto del kit?')) {
        return;
    }

    try {
        const url = `/admin/catalogo/kits/${currentKitId}/productos/${productId}`;

        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            await loadProductsInKit();
        } else {
            alert(data.message || 'Error al eliminar.');
        }
    } catch (error) {
        alert('Ocurrió un error al eliminar.');
    }
}
