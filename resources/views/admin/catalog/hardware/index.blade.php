@extends('layouts.backoffice')

@section('title', 'Hardware Base | Sensoría Backoffice')
@section('header', 'Gestión de Hardware Base')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('css/admin/products/hardware.css') }}">
@endpush

@section('header-actions')
    <button type="button" class="btn-sensoria" onclick="openHardwareModal()">
        <i class="icon-plus"></i> Nuevo Hardware
    </button>
@endsection

@section('content')
    <div class="glass-surface p-6">
        <table id="hardwareTable" class="display responsive nowrap w-full">
            <thead>
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>SKU</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($hardware as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        @if($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" style="width: 40px; height: 40px; object-fit: contain; border-radius: 4px;">
                        @else
                            <i class="icon-device" style="font-size: 1.5rem; color: #9ca3af;"></i>
                        @endif
                    </td>
                    <td style="font-family: monospace; color: #4b5563;">{{ $item->sku }}</td>
                    <td style="font-weight: 600;">{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td style="font-weight: 700; color: var(--color-primary);">${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->stock }}</td>
                    <td>
                        @if($item->is_active)
                            <span style="background:#dcfce7; color:#166534; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:600;">Activo</span>
                        @else
                            <span style="background:#fee2e2; color:#991b1b; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:600;">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn-sensoria-secondary text-sm"
                                data-id="{{ $item->id }}"
                                data-sku="{{ $item->sku }}"
                                data-name="{{ $item->name }}"
                                data-category="{{ $item->category }}"
                                data-price="{{ $item->price }}"
                                data-stock="{{ $item->stock }}"
                                data-desc="{{ $item->description }}"
                                data-kits="{{ json_encode($item->kits->pluck('id')) }}"
                                data-active="{{ $item->is_active ? '1' : '0' }}"
                                data-image="{{ $item->image_path ? asset('storage/' . $item->image_path) : '' }}"
                                onclick="openHardwareModal(this)">
                            <i class="icon-edit"></i> Editar
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- MODAL JS NATIVO RESPONSIVE -->
    <div id="hardwareModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Hardware</h2>
                <button type="button" class="btn-close" onclick="closeHardwareModal()">&times;</button>
            </div>

            <form id="hardwareForm" method="POST" action="#" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="hardwareId" value="">

                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert-danger-custom">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Fila 1: SKU y Nombre -->
                    <div class="form-row form-row-2">
                        <div class="form-group mb-0">
                            <label for="sku">SKU</label>
                            <input type="text" id="sku" name="sku" class="form-control" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="name">Nombre del Hardware</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                    </div>

                    <!-- Fila 2: Categoría, Precio y Stock -->
                    <div class="form-row form-row-3">
                        <div class="form-group mb-0">
                            <label for="category">Categoría</label>
                            <input type="text" id="category" name="category" class="form-control" placeholder="Ej. SENSOR" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="price">Precio ($)</label>
                            <input type="number" id="price" name="price" step="0.01" class="form-control" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="stock">Stock Base</label>
                            <input type="number" id="stock" name="stock" class="form-control" required>
                        </div>
                    </div>

                    <!-- Fila 3: Descripción -->
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Fila 4: Relación con Kits -->
                    <div class="form-group" style="position: relative; z-index: 10;">
                        <label for="kits" style="margin-bottom: 0.5rem; display:block;">Incluir en Kits Comerciales (Opcional)</label>
                        <select id="kits" name="kits[]" multiple>
                            @foreach($kits as $kit)
                                <option value="{{ $kit->id }}">{{ $kit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fila 5: Imagen con Vista Previa -->
                    <div class="form-group">
                        <label for="image">Imagen del Hardware (Opcional)</label>
                        <div class="image-preview-container">
                            <div class="image-preview-box" id="previewBox" onclick="openFullscreenImage()" title="Click para ver en pantalla completa">
                                <span id="previewPlaceholder" class="image-preview-placeholder"><i class="icon-device"></i></span>
                                <img id="imagePreview" src="#" alt="Vista previa" style="display: none;" />
                            </div>
                            <div style="flex: 1;">
                                <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewSelectedImage(event)">
                                <small style="color: #6b7280; font-size: 0.75rem; margin-top: 4px; display: block;">Formatos permitidos: PNG, JPG, WEBP. Máx: 2MB.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Fila 6: Estado (Toggle Switch) -->
                    <div class="form-group mb-0">
                        <label class="toggle-container">
                            <div class="toggle-switch">
                                <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <span class="toggle-slider"></span>
                            </div>
                            <span id="toggleText">Hardware Activo</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-sensoria-outline" onclick="closeHardwareModal()">Cancelar</button>
                    <button type="submit" class="btn-sensoria">Guardar Hardware</button>
                </div>
            </form>
        </div>
    </div>

    <!-- LIGHTBOX PARA PANTALLA COMPLETA -->
    <div id="fullscreenImageModal" class="image-modal-overlay" style="display: none;" onclick="closeFullscreenImage()">
        <img id="fullscreenImage" class="image-modal-content" src="" alt="Vista ampliada">
    </div>
@endsection

@push('scripts')
    <!-- JS de Choices.js -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        let kitsChoiceInstance;

        $(document).ready(function() {
            $('#hardwareTable').DataTable();

            const element = document.getElementById('kits');
            kitsChoiceInstance = new Choices(element, {
                removeItemButton: true,
                searchPlaceholderValue: 'Busca y selecciona kits...',
                placeholderValue: 'Selecciona uno o más kits...',
                itemSelectText: 'Presiona para seleccionar',
                noResultsText: 'No se encontraron kits'
            });

            @if ($errors->any())
            document.getElementById('hardwareModal').style.display = 'flex';
            @endif

            document.getElementById('hardwareForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') event.preventDefault();
            });
        });

        function openHardwareModal(btn = null) {
            const modal = document.getElementById('hardwareModal');
            const form = document.getElementById('hardwareForm');
            const formMethod = document.getElementById('formMethod');
            const title = document.getElementById('modalTitle');
            const activeCheckbox = document.getElementById('is_active');

            const previewImg = document.getElementById('imagePreview');
            const placeholder = document.getElementById('previewPlaceholder');

            if (btn) {
                title.innerText = 'Editar Hardware';
                formMethod.value = 'PUT';
                form.action = `/admin/Products/hardware/${btn.dataset.id}`;

                document.getElementById('hardwareId').value = btn.dataset.id;
                document.getElementById('sku').value = btn.dataset.sku;
                document.getElementById('name').value = btn.dataset.name;
                document.getElementById('category').value = btn.dataset.category;
                document.getElementById('price').value = btn.dataset.price;
                document.getElementById('stock').value = btn.dataset.stock;
                document.getElementById('description').value = btn.dataset.desc;

                activeCheckbox.checked = (btn.dataset.active === '1');

                if (kitsChoiceInstance) {
                    kitsChoiceInstance.removeActiveItems();
                    const kitIds = JSON.parse(btn.dataset.kits || '[]');
                    kitsChoiceInstance.setChoiceByValue(kitIds.map(String));
                }

                // Cargar la imagen existente en la vista previa
                if (btn.dataset.image) {
                    previewImg.src = btn.dataset.image;
                    previewImg.style.display = 'block';
                    placeholder.style.display = 'none';
                } else {
                    previewImg.src = '#';
                    previewImg.style.display = 'none';
                    placeholder.style.display = 'flex';
                }

            } else {
                title.innerText = 'Nuevo Hardware';
                formMethod.value = 'POST';
                form.action = `/admin/Products/hardware`;
                form.reset();

                activeCheckbox.checked = true;

                if (kitsChoiceInstance) kitsChoiceInstance.removeActiveItems();
                document.getElementById('hardwareId').value = '';

                // Limpiar la vista previa
                previewImg.src = '#';
                previewImg.style.display = 'none';
                placeholder.style.display = 'flex';
            }
            modal.style.display = 'flex';
        }

        function closeHardwareModal() {
            document.getElementById('hardwareModal').style.display = 'none';
            document.getElementById('hardwareForm').reset();
            if (kitsChoiceInstance) kitsChoiceInstance.removeActiveItems();
        }

        function previewSelectedImage(event) {
            const file = event.target.files[0];
            const previewImg = document.getElementById('imagePreview');
            const placeholder = document.getElementById('previewPlaceholder');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    placeholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        function openFullscreenImage() {
            const previewImg = document.getElementById('imagePreview');
            const fullscreenModal = document.getElementById('fullscreenImageModal');
            const fullscreenImg = document.getElementById('fullscreenImage');

            if (previewImg.style.display === 'block' && previewImg.src) {
                fullscreenImg.src = previewImg.src;
                fullscreenModal.style.display = 'flex';
            }
        }

        function closeFullscreenImage() {
            document.getElementById('fullscreenImageModal').style.display = 'none';
        }
    </script>
@endpush
