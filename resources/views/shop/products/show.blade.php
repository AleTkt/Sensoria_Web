@extends('layouts.public')

@section('title', $item->name . ' | Sensoría')

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
            <div class="product-card glass-surface detail-layout" style="margin-bottom: 2rem;">

                <!-- Ilustración -->
                <div class="product-image-container">
                    @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="product-detail-img">
                    @else
                        <i class="{{ $isKit ? 'fa-solid fa-box-open' : 'fa-solid fa-microchip' }} placeholder-icon"></i>
                        <span class="placeholder-text">{{ $isKit ? 'Kit Modular Sensoría' : 'Dispositivo IoT Sensoría' }}</span>
                    @endif
                </div>

                <!-- Información y Compras -->
                <div>
                    <span class="badge-category" style="{{ $isKit ? 'background-color: var(--color-primary); color: white;' : '' }}">
                        {{ $isKit ? 'Kit Especializado (' . ($item->target_sector ?? 'General') . ')' : ($item->category->name ?? 'Dispositivo') }}
                    </span>
                    <h1 class="detail-title">{{ $item->name }}</h1>
                    <p class="detail-price">${{ number_format($item->price, 2) }}</p>
                    <div class="detail-desc">
                        <p>{{ $item->description }}</p>
                    </div>

                    <!-- Formulario de Carrito -->
                    <form action="{{ route('shop.cart.add', $item->id) }}" method="POST" class="form-add-cart form-with-loading">
                        @csrf
                        <label for="quantity" style="display:none;">Cantidad</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" class="qty-input">
                        <button type="submit" class="btn-add-cart btn-submit-loading">
                            <i class="fa-solid fa-cart-plus"></i> {{ $isKit ? 'Añadir Kit al Carrito' : 'Añadir al Carrito' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Si es un Kit, mostramos los productos que incluye consultados por la tabla pivote kit_product -->
            @if($isKit && $item->products->count() > 0)
                <div class="product-card glass-surface" style="padding: 2rem;">
                    <h3 style="color: var(--color-tertiary); font-weight: 700; margin-bottom: 1rem; font-size: 1.25rem;">
                        <i class="fa-solid fa-layer-group"></i> Componentes incluidos en este Kit:
                    </h3>
                    <ul style="list-style: none; padding: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
                        @foreach($item->products as $productPart)
                            <li style="background: rgba(255, 255, 255, 0.8); padding: 1rem; border-radius: 8px; border: 1px solid rgba(88, 148, 69, 0.15); display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: var(--color-tertiary); display: block; font-size: 0.95rem;">{{ $productPart->name }}</strong>
                                    <span style="font-size: 0.8rem; color: #6b7280;">SKU: {{ $productPart->sku ?? 'N/D' }}</span>
                                </div>
                                <span style="background-color: rgba(88, 148, 69, 0.15); color: var(--color-tertiary); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: bold; font-size: 0.85rem;">
                                    x{{ $productPart->pivot->quantity }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
@endsection
