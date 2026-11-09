@extends('layouts.public')

@section('title', 'Kits y Soluciones IoT | Sensoría')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/shop/product-detail.css') }}">
@endpush

@section('content')
    <div class="shop-page-wrapper">
        <div class="shop-container">

            <!-- Encabezado comercial -->
            <div class="shop-header">
                <h1 class="page-title">Kits Modulares & Dispositivos IoT</h1>
                <p class="page-subtitle">Soluciones Plug & Play para automatizar, asegurar y monitorear tu PYME.</p>
            </div>

            <!-- Rejilla de Productos / Kits en CSS Puro -->
            <div class="shop-grid">
                @forelse($products as $product)
                    <div class="product-card glass-surface">

                        @if($product->image_path)
                            <div class="product-image-preview">
                                <!-- Asumiendo que usarás el Storage público de Laravel/Supabase -->
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="product-img">
                            </div>
                        @else
                            <div class="product-image-preview">
                                <i class="icon-device placeholder-icon-small"></i>
                            </div>
                        @endif

                        <div>
                            <span class="badge-category">{{ $product->category->name ?? 'Dispositivo' }}</span>
                            <h3 class="card-title">{{ $product->name }}</h3>
                            <p class="card-desc">{{ $product->description }}</p>
                        </div>

                            <div class="card-footer">
                                <span class="card-price">${{ number_format($product->price, 2) }}</span>

                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <!-- Botón secundario para ver detalle -->
                                    <a href="{{ route('shop.products.show', $product->id) }}" class="btn-sensoria" style="width: 42px; height: 42px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px;" title="Ver detalle">
                                        <i class="fa-regular fa-eye" style="font-size: 1rem; color: #FFFFFF;"></i>
                                    </a>

                                    <!-- Formulario principal con las clases para loading-buttons.js -->
                                    <form action="{{ route('shop.cart.add', $product->id) }}" method="POST" class="form-with-loading" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-sensoria btn-submit-loading">
                                            <i class="fa-solid fa-cart-plus"></i> Agregar
                                        </button>
                                    </form>
                                </div>
                            </div>

                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem 0; color: #6b7280;">
                        <p>No hay productos ni kits disponibles por el momento.</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            <div style="margin-top: 3rem;">
                {{ $products->links() }}
            </div>

        </div>
    </div>
@endsection
