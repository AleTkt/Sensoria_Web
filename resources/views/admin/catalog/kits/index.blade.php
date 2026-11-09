@extends('layouts.backoffice')

@section('title', 'Kits Comerciales | Sensoría Backoffice')
@section('header', 'Gestión de Kits Comerciales')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/products/kit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <!-- Botón Editar -->
                            <button type="button" class="btn-sensoria-secondary text-sm"
                                    data-id="{{ $kit->id }}"
                                    data-sku="{{ $kit->sku }}"
                                    data-name="{{ $kit->name }}"
                                    data-sector="{{ $kit->target_sector }}"
                                    data-price="{{ $kit->price }}"
                                    data-desc="{{ $kit->description }}"
                                    data-active="{{ $kit->is_active ? '1' : '0' }}"
                                    onclick="openKitModal(this)">
                                <i class="icon-edit"></i> Editar
                            </button>

                            <!-- Botón Activar/Desactivar -->
                            <form id="toggle-form-{{ $kit->id }}" action="{{ route('admin.catalog.kits.toggle', $kit->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('PATCH')
                                @if($kit->is_active)
                                    <button type="button" class="text-sm"
                                            style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 0.4rem 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;"
                                            onclick="openConfirmModal('{{ $kit->id }}', 'desactivar', '{{ $kit->name }}')"
                                            title="Desactivar este kit">
                                        <i class="fa-solid fa-power-off"></i> Desactivar
                                    </button>
                                @else
                                    <button type="button" class="text-sm"
                                            style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 0.4rem 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;"
                                            onclick="openConfirmModal('{{ $kit->id }}', 'activar', '{{ $kit->name }}')"
                                            title="Activar este kit">
                                        <i class="fa-solid fa-check"></i> Activar
                                    </button>
                                @endif
                            </form>
                        </div>
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

            <form id="kitForm" method="POST" action="#" class="form-with-loading" novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="kitId" value="">

                <div class="modal-body">
                    <!-- Fila 1: SKU y Nombre -->
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

                    <!-- Fila 2: Sector Objetivo y Precio -->
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

                    <!-- Fila 4: Toggle de Estado Activo -->
                    <div class="form-group mb-0">
                        <label class="toggle-container">
                            <div class="toggle-switch">
                                <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <span class="toggle-slider"></span>
                            </div>
                            <span id="toggleText">Kit Activo en Tienda</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-sensoria-outline" onclick="closeKitModal()">Cancelar</button>
                    <button type="submit" class="btn-sensoria btn-submit-loading">Guardar Kit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN PARA ACTIVAR/DESACTIVAR -->
    <div id="confirmToggleModal" class="modal-overlay" style="display: none; z-index: 10001;">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h2 id="confirmModalTitle">Confirmar Acción</h2>
                <button type="button" class="btn-close" onclick="closeConfirmModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p id="confirmModalMessage" style="margin-top: 0; color: #374151; font-size: 0.95rem; line-height: 1.5;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sensoria-outline" onclick="closeConfirmModal()">Cancelar</button>
                <button type="button" id="btnConfirmToggle" class="btn-sensoria" onclick="submitToggleForm()">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- CONTENEDOR DE TOASTS REUTILIZABLES -->
    <div class="toast-container-brutal" id="toast-container">
        @if(session('success'))
            <div class="toast-brutal toast-success">
                <div class="toast-header">
                    <i class="fa-solid fa-circle-check"></i>
                    <strong>¡Éxito!</strong>
                    <button class="btn-close" type="button">&times;</button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="toast-brutal toast-error">
                <div class="toast-header">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <strong>Error de validación</strong>
                    <button class="btn-close" type="button">&times;</button>
                </div>
                <div class="toast-body">
                    {{ $errors->first() }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/loading-buttons.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/admin/kits-ajax.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('#kitsTable').DataTable();

            @if ($errors->any())
            document.getElementById('kitModal').style.display = 'flex';
            @endif

            document.getElementById('kitForm').addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                    event.preventDefault();
                }
            });
        });



    </script>




@endpush
