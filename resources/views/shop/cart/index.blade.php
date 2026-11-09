@extends('layouts.public')

@section('title', 'Carrito de Compras | Sensoría')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/shop/product-detail.css') }}">
@endpush

@section('content')
    <div class="shop-page-wrapper">
        <div class="shop-container">

            <a href="{{ route('shop.products.index') }}" class="back-link">
                ← Seguir Comprando
            </a>

            <h1 class="page-title" style="margin-bottom: 2rem;">Tu Carrito de Compras</h1>

            @if(session('success'))
                <div style="background-color: rgba(88, 148, 69, 0.15); color: var(--color-tertiary); padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($cart) > 0)
                <div class="product-card glass-surface" style="margin-bottom: 2rem;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.1);">
                            <th style="padding: 1rem; color: var(--color-tertiary);">Ítem / Solución IoT</th>
                            <th style="padding: 1rem; color: var(--color-tertiary);">Tipo</th>
                            <th style="padding: 1rem; color: var(--color-tertiary);">Precio</th>
                            <th style="padding: 1rem; color: var(--color-tertiary);">Cantidad</th>
                            <th style="padding: 1rem; color: var(--color-tertiary);">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $total = 0; @endphp
                        @foreach($cart as $key => $details)
                            @php
                                $subtotal = $details['price'] * $details['quantity'];
                                $total += $subtotal;
                                $isKitItem = $details['is_kit'] ?? false;
                            @endphp
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="padding: 1rem; color: #4b5563; font-weight: 600;">
                                    {{ $details['name'] }}
                                </td>

                                <td style="padding: 1rem;">
                                    @if($isKitItem)
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; background-color: var(--color-primary); color: white; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">
                                            <i class="fa-solid fa-box-open"></i> Kit Modular
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; background-color: rgba(88, 148, 69, 0.15); color: var(--color-tertiary); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">
                                            <i class="fa-solid fa-microchip"></i> Dispositivo
                                        </span>
                                    @endif
                                </td>

                                <td style="padding: 1rem; color: #4b5563;">${{ number_format($details['price'], 2) }}</td>
                                <td style="padding: 1rem; color: #4b5563;">{{ $details['quantity'] }}</td>
                                <td style="padding: 1rem; font-weight: bold; color: var(--color-tertiary);">
                                    ${{ number_format($subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div style="text-align: right; padding: 1.5rem; font-size: 1.5rem; font-weight: 800; color: var(--color-primary);">
                        Total: ${{ number_format($total, 2) }}
                    </div>
                </div>

                <div style="text-align: right;">
                    @guest
                        <div style="margin-bottom: 1rem; color: #4b5563; font-size: 0.875rem;">
                            Para proceder con el pago y enlazar estos equipos a su empresa PYME, requiere iniciar sesión.
                        </div>
                        <a href="{{ route('login') }}" class="btn-sensoria">
                            Iniciar Sesión para Pagar
                        </a>
                    @endguest

                    @auth
                        <form action="#" method="POST" class="form-with-loading" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-sensoria btn-submit-loading" style="background-color: var(--color-tertiary);">
                                Proceder al Pago
                            </button>
                        </form>
                    @endauth
                </div>

            @else
                <div class="product-card glass-surface" style="text-align: center; padding: 4rem 2rem;">
                    <i class="fa-solid fa-cart-arrow-down placeholder-icon" style="opacity: 0.2; font-size: 4rem; color: var(--color-tertiary);"></i>
                    <h3 class="card-title" style="margin-top: 1rem;">Tu carrito está vacío</h3>
                    <p class="card-desc">Agrega soluciones IoT para comenzar a digitalizar tu empresa.</p>
                    <a href="{{ route('shop.products.index') }}" class="btn-sensoria" style="display: inline-block; margin-top: 1rem;">
                        Explorar Kits Modulares
                    </a>
                </div>
            @endif

        </div>
    </div>
@endsection
