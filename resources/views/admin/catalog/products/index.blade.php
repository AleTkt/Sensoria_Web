@extends('layouts.backoffice')

@section('title', 'Gestión de Kits y Productos | Sensoría Backoffice')
@section('header', 'Gestión de Kits y Productos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/products/product-in-kit.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


@section('content')
    <div class="sensoria-card">
        <div style="overflow-x: auto;">
            <table id="kitProductsTable" class="display responsive nowrap" style="width:100%">
                <thead>
                <tr>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">ID</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Nombre del Kit</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">SKU</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A);">Productos Incluidos</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A); text-align: right;">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($kits as $kit)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 1rem; font-weight: 700; color: #4b5563;">{{ $kit->id }}</td>
                        <td style="padding: 1rem; font-weight: 700; color: var(--color-tertiary, #1B3D1A); font-size: 1rem;">{{ $kit->name }}</td>
                        <td style="padding: 1rem; font-family: monospace; color: #4b5563; font-size: 0.88rem;">{{ $kit->sku }}</td>
                        <td style="padding: 1rem;">
                            @if($kit->products->count() > 0)
                                <span style="background: rgba(88, 148, 69, 0.12); color: var(--color-tertiary, #1B3D1A); padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 800; border: 1px solid rgba(88, 148, 69, 0.25);">
                                    {{ $kit->products->count() }} {{ $kit->products->count() === 1 ? 'producto' : 'productos' }}
                                </span>
                            @else
                                <span style="color: #9ca3af; font-size: 0.82rem; font-style: italic;">Sin productos</span>
                            @endif
                        </td>

                        <td style="padding: 1rem; text-align: right;">
                            <button type="button" class="btn-sensoria-action"
                                    onclick="openManageKitModal({{ $kit->id }}, '{{ $kit->name }}')"
                                    title="Gestionar productos de este kit">
                                <i class="fa-solid fa-boxes-stacked" style="color: var(--color-primary, #589445);"></i> Gestionar Productos
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DE GESTIÓN DE PRODUCTOS POR KIT (MANTENIDO INTACTO) -->
    <div id="manageKitModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="manageModalTitle">Gestionar Productos: Kit <span id="kitNameDisplay"></span></h2>
                <button type="button" class="btn-close" onclick="closeManageKitModal()">&times;</button>
            </div>

            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Columna Izquierda: Productos Agregados -->
                    <div>
                        <h3 style="margin-bottom: 1rem; font-size: 1rem; color: var(--color-tertiary); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            Productos agregados en el kit
                        </h3>
                        <div id="productsInKitList" style="max-height: 400px; overflow-y: auto;">
                            <p style="color: #6b7280; text-align: center; padding: 2rem;">Cargando...</p>
                        </div>
                    </div>

                    <!-- Columna Derecha: Agregar Productos -->
                    <div>
                        <h3 style="margin-bottom: 1rem; font-size: 1rem; color: var(--color-tertiary); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-circle-plus"></i>
                            Agregar nuevos productos al kit
                        </h3>

                        <!-- Filtros de Categoría -->
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: #374151;">
                                Selecciona la categoría
                            </label>
                            <div id="categoryFilters" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <button type="button" class="category-filter-btn active" data-category="all" onclick="filterProductsByCategory('all')">
                                    Todos
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Productos Disponibles -->
                        <div style="margin-bottom: 1rem; max-height: 200px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px;">
                            <div id="availableProductsList">
                                <p style="color: #6b7280; text-align: center; padding: 1rem;">Cargando...</p>
                            </div>
                        </div>

                        <!-- Input de Cantidad -->
                        <div style="margin-bottom: 1rem;">
                            <label for="productQuantity" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: #374151;">
                                Ingrese la cantidad
                            </label>
                            <input type="number" id="productQuantity" min="1" value="1"
                                   style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem;">
                        </div>

                        <!-- Botón Agregar -->
                        <button type="button" onclick="addProductsToKit()"
                                class="btn-sensoria" style="width: 100%;">
                            <i class="fa-solid fa-plus"></i> Agregar al Kit
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-sensoria-outline" onclick="closeManageKitModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN AL AGREGAR A KITS (MANTENIDO INTACTO) -->
    <div id="confirmAddKitModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>Agregar a Kits</h2>
                <button type="button" class="btn-close" onclick="closeConfirmAddModal()">&times;</button>
            </div>

            <div class="modal-body">
                <p id="confirmAddMessage" style="font-size: 1rem; color: #374151; line-height: 1.5; text-align: center;">
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-sensoria-outline" onclick="closeConfirmAddModal()">Cerrar</button>
                <button type="button" class="btn-sensoria" onclick="confirmAddProducts()">Sí, agregar</button>
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

        @if(session('error'))
            <div class="toast-brutal toast-error">
                <div class="toast-header">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <strong>Error</strong>
                    <button class="btn-close" type="button">&times;</button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/manage-kit-products.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#kitProductsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[0, 'desc']]
            });
        });
    </script>
@endpush
