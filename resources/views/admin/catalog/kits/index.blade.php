@extends('layouts.backoffice')

@section('title', 'Kits Comerciales | Sensoría Backoffice')
@section('header', 'Gestión de Kits Comerciales')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/products/kit.css') }}">
@endpush

@section('header-actions')
    <button type="button" class="btn-sensoria" onclick="openKitModal()">
        <i class="icon-plus"></i> Nuevo Kit
    </button>
@endsection

@section('content')
    <div class="glass-surface" style="padding: 1.5rem;">
        <table id="kitsTable" class="display responsive nowrap w-full">
            <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Nombre del Kit</th>
                <th>Sector Objetivo</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($kits as $kit)
                <tr>
                    <td>{{ $kit->id }}</td>
                    <td style="font-family: monospace; color: #4b5563;">{{ $kit->sku }}</td>
                    <td style="font-weight: 600;">{{ $kit->name }}</td>
                    <td>{{ $kit->target_sector }}</td>
                    <td style="font-weight: 700; color: var(--color-primary);">${{ number_format($kit->price, 2) }}</td>
                    <td>
                        @if($kit->is_active)
                            <span style="background:#dcfce7; color:#166534; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:600;">Activo</span>
                        @else
                            <span style="background:#fee2e2; color:#991b1b; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:600;">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn-sensoria-secondary text-sm"
                                data-id="{{ $kit->id }}"
                                data-sku="{{ $kit->sku }}"
                                data-name="{{ $kit->name }}"
                                data-sector="{{ $kit->target_sector }}"
                                data-price="{{ $kit->price }}"
                                data-desc="{{ $kit->description }}"
                                onclick="openKitModal(this)">
                            <i class="icon-edit"></i> Editar
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- MODAL JS NATIVO RESPONSIVE PARA KITS -->
    <div id="kitModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Kit Comercial</h2>
                <button type="button" class="btn-close" onclick="closeKitModal()">&times;</button>
            </div>

            <form id="kitForm" method="POST" action="#">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="kitId" value="">

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

                    <!-- Fila 1: SKU y Nombre (Responsive: 1 col en móvil, 2 cols en escritorio) -->
                    <div class="form-row form-row-2">
                        <div class="form-group mb-0">
                            <label for="sku">SKU</label>
                            <input type="text" id="sku" name="sku" class="form-control" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="name">Nombre del Kit</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                    </div>

                    <!-- Fila 2: Sector Objetivo y Precio (Responsive: 2 columnas iguales) -->
                    <div class="form-row form-row-equal">
                        <div class="form-group mb-0">
                            <label for="sector">Sector Objetivo (Ej. Comercio Seguro)</label>
                            <input type="text" id="sector" name="target_sector" class="form-control" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="price">Precio del Kit ($)</label>
                            <input type="number" id="price" name="price" step="0.01" class="form-control" required>
                        </div>
                    </div>

                    <!-- Fila 3: Descripción Comercial -->
                    <div class="form-group">
                        <label for="description">Descripción Comercial</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Fila 4: Estado Activo -->
                    <div class="form-group mb-0">
                        <label class="checkbox-label">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <span>Kit Activo en Tienda</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-sensoria-outline" onclick="closeKitModal()">Cancelar</button>
                    <button type="submit" class="btn-sensoria">Guardar Kit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#kitsTable').DataTable();

            @if ($errors->any())
            document.getElementById('kitModal').style.display = 'flex';
            @endif

            document.getElementById('kitForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') event.preventDefault();
            });
        });

        function openKitModal(btn = null) {
            const modal = document.getElementById('kitModal');
            const form = document.getElementById('kitForm');
            const formMethod = document.getElementById('formMethod');
            const title = document.getElementById('modalTitle');

            if (btn) {
                title.innerText = 'Editar Kit';
                formMethod.value = 'PUT';
                form.action = `/admin/catalogo/kits/${btn.dataset.id}`;

                document.getElementById('kitId').value = btn.dataset.id;
                document.getElementById('sku').value = btn.dataset.sku;
                document.getElementById('name').value = btn.dataset.name;
                document.getElementById('sector').value = btn.dataset.sector;
                document.getElementById('price').value = btn.dataset.price;
                document.getElementById('description').value = btn.dataset.desc;
            } else {
                title.innerText = 'Nuevo Kit';
                formMethod.value = 'POST';
                form.action = `/admin/catalogo/kits`;
                form.reset();
                document.getElementById('kitId').value = '';
            }
            modal.style.display = 'flex';
        }

        function closeKitModal() {
            document.getElementById('kitModal').style.display = 'none';
            document.getElementById('kitForm').reset();
        }
    </script>
@endpush
