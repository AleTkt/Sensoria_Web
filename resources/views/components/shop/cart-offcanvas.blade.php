<!-- resources/views/components/shop/cart-offcanvas.blade.php -->
<div class="cart-sidebar-overlay" id="cartOverlay" onclick="toggleCart()"></div>
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-sidebar-header">
        <h2 style="margin: 0; font-size: 1.25rem;">Mi Carrito ({{ $cartCount ?? 0 }})</h2>
        <button class="close-cart-btn" onclick="toggleCart()">&times;</button>
    </div>

    <div class="cart-sidebar-content">
        @if(isset($cart) && count($cart) > 0)
            @foreach($cart as $id => $details)
                <div class="cart-item">
                    @if(isset($details['image_path']) && $details['image_path'])
                        <img src="{{ asset('storage/' . $details['image_path']) }}" alt="{{ $details['name'] }}" class="cart-item-img">
                    @else
                        <div class="cart-item-img" style="display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-microchip" style="color: var(--color-tertiary); opacity: 0.3; font-size: 1.5rem;"></i>
                        </div>
                    @endif

                    <div style="flex: 1;">
                        <h4 style="font-weight: 700; color: var(--color-tertiary); font-size: 0.875rem; margin: 0 0 0.25rem 0;">{{ $details['name'] }}</h4>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                            <span style="font-size: 0.875rem; color: #4b5563;">Cant: {{ $details['quantity'] }}</span>
                            <span style="color: var(--color-primary); font-weight: 700; font-size: 0.875rem;">${{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 2rem 0; color: #6b7280;">
                <i class="fa-solid fa-cart-arrow-down" style="margin: 0 auto 1rem; font-size: 3rem; opacity: 0.3; display: block;"></i>
                <p>Tu carrito está vacío.</p>
            </div>
        @endif
    </div>

    <div class="cart-sidebar-footer">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #4b5563;">
            <span>Subtotal</span>
            <span>${{ number_format($cartTotal ?? 0, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; color: var(--color-tertiary); margin-bottom: 1.5rem; padding-top: 0.5rem; border-top: 2px solid var(--color-neutral);">
            <span>Total</span>
            <span>${{ number_format($cartTotal ?? 0, 2) }}</span>
        </div>

        @if(isset($cart) && count($cart) > 0)
            @guest
                <a href="{{ route('login') }}" style="display: block; text-align: center; width: 100%; padding: 1rem; background-color: var(--color-tertiary); color: white; border-radius: 8px; text-decoration: none; font-weight: bold;">
                    Iniciar Sesión para Pagar
                </a>
                <p style="text-align: center; font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">
                    Debe iniciar sesión para enlazar los equipos a su PYME.
                </p>
            @endguest
            @auth
                <!-- Este botón redirige a la vista completa de tu carrito (shop/cart/index.blade.php) -->
                <a href="{{ route('shop.cart.index') }}" style="display: block; text-align: center; width: 100%; padding: 1rem; background-color: var(--color-primary); color: white; border-radius: 8px; text-decoration: none; font-weight: bold;">
                    Revisar Pedido Completo
                </a>
            @endauth
        @endif
    </div>
</div>
