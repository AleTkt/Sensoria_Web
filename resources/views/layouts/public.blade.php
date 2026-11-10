<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sensoria IoT'))</title>

    <!-- CSS Puro -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/animations.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/public.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}">

    <!-- CSS del Componente Carrito -->
    <link rel="stylesheet" href="{{ asset('css/components/cart-offcanvas.css') }}">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body>

<!-- Lógica PHP para obtener el total del carrito (Disponible en todo el Navbar) -->
@php
    $cart = session()->get('cart', []);
    $cartCount = array_sum(array_column($cart, 'quantity'));
    $cartTotal = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $cart));
@endphp

<div class="public-layout-container">

    <!-- Navbar -->
    <div class="navbar-wrapper">
        <nav class="navbar animate-fade-in-up">
            <div class="brand">
                <a href="{{ url('/') }}" style="text-decoration: none;">
                    <h1>Sensoría <span>IoT</span></h1>
                </a>
            </div>

            <ul class="nav-links">
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Inicio</a></li>
                <li>
                    <a href="{{ route('shop.products.index') }}" class="{{ request()->routeIs('shop.products.*') ? 'active' : '' }}">
                        Kits Modulares
                    </a>
                </li>
            </ul>

            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <!-- Botón Disparador del Carrito Off-canvas -->
                <div class="nav-cart-trigger" onclick="toggleCart()">
                    <div style="position: relative;">
                        <i class="fa-solid fa-cart-shopping" style="font-size: 1.2rem; color: var(--color-tertiary);"></i>
                        @if($cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </div>
                    <span style="font-weight: 700; color: var(--color-tertiary); font-size: 0.95rem;">
                        ${{ number_format($cartTotal, 2) }}
                    </span>
                </div>

                <!-- Menú de Usuario -->
                @auth
                    <div class="dropdown-container">
                        <button type="button" id="user-menu-btn" onclick="toggleUserDropdown(event)" style="background: var(--color-primary); color: white; border: none; padding: 10px 18px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-regular fa-circle-user"></i> {{ Auth::user()->name }} <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem;"></i>
                        </button>

                        <div id="user-dropdown-menu" class="dropdown-menu" style="display: none;">

                            @if(in_array(Auth::user()->global_role, ['ADMIN', 'SOPORTE']))
                                <a href="{{ route('admin.dashboard') }}" style="font-weight: 700; color: #e53e3e;">
                                    <i class="fa-solid fa-shield-halved"></i>  Ir al Backoffice
                                </a>
                                <div class="dropdown-divider"></div>
                            @endif

                            <a href="{{ route('company.edit') }}" style="font-weight: 600; color: var(--color-tertiary);">
                                <i class="fa-solid fa-building"></i>  Mi Espacio de Trabajo
                            </a>

                            <div class="dropdown-divider"></div>

                                <a href="{{ route('client.orders.index') }}" style="color: var(--color-tertiary); font-weight: 600;"><i class="fa-solid fa-box"></i>  Mis Pedidos</a>
                                <a href="{{ route('wallet.index') }}" style="color: var(--color-tertiary); font-weight: 600;"><i class="fa-solid fa-wallet"></i>  Billetera y Pagos</a>
                            <a href="{{'profile'}}"><i class="fa-solid fa-user-gear"></i>  Ajustes de Perfil</a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}" class="form-with-loading">
                                @csrf
                                <button type="submit" class="btn-submit-loading" style="background: none; border: none; width: 100%; text-align: left; padding: 10px 16px; color: #e53e3e; cursor: pointer; font-weight: 600;">
                                    <i class="fa-solid fa-power-off"></i>  Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" style="background-color: var(--color-primary); color: white; padding: 10px 20px; text-decoration: none; border-radius: var(--radius-button); font-weight: 600;">
                        Acceso Clientes <i class="fa-solid fa-right-to-bracket" style="margin-left: 5px;"></i>
                    </a>
                @endauth
            </div>
        </nav>
    </div>

    <!-- Contenido Inyectado mediante Blade Tradicional -->
    <main class="main-content animate-fade-in-up" style="animation-delay: 0.1s;">
        @yield('content')
    </main>

    <!-- Footer Opcional -->
    <footer class="public-footer">
        <div class="footer-bottom">
            &copy; {{ date('Y') }} Sensoría IoT. Todos los derechos reservados.
        </div>
    </footer>

</div> <!-- Fin de public-layout-container -->

<!-- Componentes Globales -->
@include('layouts.toast')

<!-- Inclusión del Componente del Carrito (¡FUERA DEL CONTENEDOR PRINCIPAL!) -->
@include('components.shop.cart-offcanvas', ['cart' => $cart, 'cartCount' => $cartCount, 'cartTotal' => $cartTotal])

<!-- JavaScript Nativo -->
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/loading-buttons.js') }}"></script>
<script>
    // Lógica menú usuario
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('user-dropdown-menu');
        const isVisible = dropdown.style.display === 'block';
        dropdown.style.display = isVisible ? 'none' : 'block';
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('user-dropdown-menu');
        if (dropdown && dropdown.style.display === 'block') {
            const btn = document.getElementById('user-menu-btn');
            if (!dropdown.contains(event.target) && event.target !== btn) {
                dropdown.style.display = 'none';
            }
        }
    });

    // Lógica del Panel Off-canvas
    function toggleCart() {
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('cartOverlay');

        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

        // Prevenir scroll en el body cuando el carrito está abierto
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
</script>

@stack('scripts')
</body>
</html>
