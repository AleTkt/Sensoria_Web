@extends('layouts.public')

@section('title', $product->name . ' | Sensoría')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/shop/product-detail.css') }}">
@endpush

@section('content')
    <div class="shop-page-wrapper">
        <div class="shop-container">

            <!-- Enlace de regreso -->
            <a href="{{ route('shop.products.index') }}" class="back-link">
                ← Volver a la Tienda
            </a>

            <!-- Layout del detalle usando Grid puro -->
            <div class="product-card glass-surface detail-layout">

                <!-- Ilustración del Producto (Aplicando directiva ESTRICTA de íconos) -->
                <div class="product-image-container">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="product-detail-img">
                    @else
                        <i class="icon-device placeholder-icon"></i>
                        <span class="placeholder-text">Dispositivo IoT Sensoría</span>
                    @endif
                </div>

                <!-- Información y Compras -->
                <div>
                    <span class="badge-category">{{ $product->category->name ?? 'Dispositivo' }}</span>
                    <h1 class="detail-title">{{ $product->name }}</h1>
                    <p class="detail-price">${{ number_format($product->price, 2) }}</p>
                    <div class="detail-desc">
                        <p>{{ $product->description }}</p>
                    </div>

                    <!-- Formulario de Carrito sin Tailwind -->
                    <form action="#" method="POST" class="form-add-cart">
                        @csrf
                        <label for="quantity" style="display:none;">Cantidad</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" class="qty-input">
                        <button type="button" class="btn-add-cart">
                            Añadir al Carrito
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
@endsection
