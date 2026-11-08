@extends('layouts.backoffice')

@section('title', 'Hardware Base | Sensoría Backoffice')
@section('header', 'Gestión de Hardware Base')

@push('styles')
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

                    <!-- Fila 1: SKU y Nombre (Responsive 2 columnas) -->
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

                    <!-- Fila 2: Categoría, Precio y Stock (Responsive 3 columnas) -->
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

                    <!-- Fila 4: Imagen -->
                    <div class="form-group">
                        <label for="image">Imagen (Opcional)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>

                    <!-- Fila 5: Estado -->
                    <div class="form-group mb-0">
                        <label class="checkbox-label">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <span>Hardware Activo</span>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#hardwareTable').DataTable();

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

            if (btn) {
                title.innerText = 'Editar Hardware';
                formMethod.value = 'PUT';
                form.action = `/admin/catalogo/hardware/${btn.dataset.id}`;

                document.getElementById('hardwareId').value = btn.dataset.id;
                document.getElementById('sku').value = btn.dataset.sku;
                document.getElementById('name').value = btn.dataset.name;
                document.getElementById('category').value = btn.dataset.category;
                document.getElementById('price').value = btn.dataset.price;
                document.getElementById('stock').value = btn.dataset.stock;
                document.getElementById('description').value = btn.dataset.desc;
            } else {
                title.innerText = 'Nuevo Hardware';
                formMethod.value = 'POST';
                form.action = `/admin/catalogo/hardware`;
                form.reset();
                document.getElementById('hardwareId').value = '';
            }
            modal.style.display = 'flex';
        }

        function closeHardwareModal() {
            document.getElementById('hardwareModal').style.display = 'none';
            document.getElementById('hardwareForm').reset();
        }
    </script>
@endpush
