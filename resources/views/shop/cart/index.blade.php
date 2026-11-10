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
                        <!-- ... (Tu código existente de la tabla del carrito se mantiene exactamente igual) ... -->
                        <thead>
                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.1);">
                            <th style="padding: 1rem; color: var(--color-tertiary);">Ítem / Solución IoT</th>
                            <th style="padding: 1rem; color: var(--color-tertiary);">Tipo</th>
                            <th style="padding: 1rem; color: var(--color-tertiary);">Precio</th>
                            <th style="padding: 1rem; color: var(--color-tertiary); text-align: center;">Cantidad</th>
                            <th style="padding: 1rem; color: var(--color-tertiary);">Subtotal</th>
                            <th style="padding: 1rem; color: var(--color-tertiary); text-align: center;">Acción</th>
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
                                <!-- ... (Tu código existente de las filas del carrito) ... -->
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="padding: 1rem; color: #4b5563; font-weight: 600;">{{ $details['name'] }}</td>
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
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255, 255, 255, 0.9); padding: 0.25rem 0.5rem; border-radius: 8px; border: 1px solid rgba(27, 61, 26, 0.15);">
                                        <form action="{{ route('shop.cart.update', $key) }}" method="POST" class="form-with-loading" style="margin: 0;">
                                            @csrf @method('PATCH') <input type="hidden" name="action" value="decrease">
                                            <button type="submit" class="btn-submit-loading" style="background: none; border: none; cursor: pointer; color: var(--color-tertiary); padding: 0.2rem 0.4rem; font-weight: bold;" title="Disminuir"><i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i></button>
                                        </form>
                                        <span style="font-weight: 700; color: var(--color-tertiary); min-width: 20px; text-align: center;">{{ $details['quantity'] }}</span>
                                        <form action="{{ route('shop.cart.update', $key) }}" method="POST" class="form-with-loading" style="margin: 0;">
                                            @csrf @method('PATCH') <input type="hidden" name="action" value="increase">
                                            <button type="submit" class="btn-submit-loading" style="background: none; border: none; cursor: pointer; color: var(--color-tertiary); padding: 0.2rem 0.4rem; font-weight: bold;" title="Aumentar"><i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i></button>
                                        </form>
                                    </div>
                                </td>
                                <td style="padding: 1rem; font-weight: bold; color: var(--color-tertiary);">${{ number_format($subtotal, 2) }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <form action="{{ route('shop.cart.remove', $key) }}" method="POST" class="form-with-loading" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-submit-loading" style="background: rgba(229, 62, 62, 0.1); color: #e53e3e; border: none; border-radius: 6px; width: 32px; height: 32px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Eliminar ítem"><i class="fa-regular fa-trash-can" style="font-size: 0.9rem;"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div style="text-align: right; padding: 1.5rem; font-size: 1.5rem; font-weight: 800; color: var(--color-primary);">
                        Total a pagar: ${{ number_format($total, 2) }}
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
                        <!-- NUEVO BOTÓN: Abre el modal en lugar de enviar el form -->
                        <button type="button" id="btn-open-checkout-modal" class="btn-sensoria" style="background-color: var(--color-tertiary);">
                            Proceder al Pago
                        </button>
                    @endauth
                </div>

            @else
                <!-- ... (Tu estado de carrito vacío se mantiene igual) ... -->
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

    <!-- EL MODAL DE CHECKOUT -->
    @auth
        @if(count($cart) > 0)
            <div id="checkout-modal" class="modal-overlay">
                <div class="checkout-modal">
                    <div class="modal-header">
                        <h3 class="modal-title"><i class="fa-solid fa-credit-card"></i> Confirmar Pago</h3>
                        <button type="button" id="btn-close-modal" class="btn-close-modal"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(88, 148, 69, 0.1); border-radius: 8px;">
                        <span style="color: #4b5563; font-size: 0.875rem;">Total a cargar:</span>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--color-primary);">
                            ${{ number_format($total, 2) }}
                        </div>
                    </div>

                    <form action="{{ route('shop.checkout.process') }}" method="POST" id="checkout-form" class="form-with-loading">
                        @csrf

                        <h4 style="margin-bottom: 1rem; color: var(--color-tertiary); font-size: 1rem;">Selecciona tu método de pago</h4>

                        @if($paymentMethods->count() > 0)
                            <div style="max-height: 200px; overflow-y: auto; margin-bottom: 1.5rem;">
                                @foreach($paymentMethods as $method)
                                    <label class="payment-method-option">
                                        <input type="radio" name="payment_method_id" value="{{ $method->id }}" required {{ $loop->first ? 'checked' : '' }}>
                                        <div class="card-details">
                                            <span class="card-brand"><i class="fa-brands fa-cc-{{ strtolower($method->brand ?? 'visa') }}"></i> Tarjeta {{ ucfirst($method->brand ?? 'Crédito') }}</span>
                                            <span class="card-last4">Termina en •••• {{ $method->last_four }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div style="text-align: right; margin-bottom: 1.5rem;">
                                <a href="{{ route('wallet.index') }}" style="color: var(--color-primary); font-size: 0.875rem; text-decoration: underline;">Administrar tarjetas</a>
                            </div>

                            <button type="submit" class="btn-sensoria btn-submit-loading" style="width: 100%; background-color: var(--color-tertiary);">
                                Confirmar y Pagar
                            </button>
                        @else
                            <div style="text-align: center; padding: 1.5rem 0;">
                                <p style="color: #4b5563; margin-bottom: 1rem;">No tienes métodos de pago registrados.</p>
                                <a href="{{ route('wallet.index') }}" class="btn-sensoria">
                                    <i class="fa-solid fa-plus"></i> Agregar Tarjeta
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('checkout-modal');
                    const btnOpen = document.getElementById('btn-open-checkout-modal');
                    const btnClose = document.getElementById('btn-close-modal');

                    // Validamos que los elementos existan antes de asignar eventos
                    if (btnOpen && modal && btnClose) {

                        // Abrir modal
                        btnOpen.addEventListener('click', function() {
                            modal.classList.add('active');
                            document.body.style.overflow = 'hidden'; // Prevenir scroll de fondo
                        });

                        // Cerrar modal
                        btnClose.addEventListener('click', closeModal);

                        // Cerrar al hacer clic fuera del modal (en el overlay)
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) {
                                closeModal();
                            }
                        });

                        // Cerrar con tecla Escape
                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape' && modal.classList.contains('active')) {
                                closeModal();
                            }
                        });
                    }

                    function closeModal() {
                        if (modal) {
                            modal.classList.remove('active');
                            document.body.style.overflow = ''; // Restaurar scroll
                        }
                    }
                });
            </script>
        @endif
    @endauth

@endsection
