<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sensoria IoT'))</title>

    <!-- CSS Puro -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/public.css') }}">

    @stack('styles')
</head>
<body>
<div class="public-layout-container">

    <!-- Navbar -->
    <div class="navbar-wrapper">
        <nav class="navbar">
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

            <div>
                @auth
                    <div class="dropdown-container">
                        <button type="button" id="user-menu-btn" onclick="toggleUserDropdown(event)" style="background: var(--color-primary); color: white; border: none; padding: 10px 18px; border-radius: var(--radius-button); font-weight: 600; cursor: pointer;">
                            {{ Auth::user()->name }} &#9662;
                        </button>

                        <div id="user-dropdown-menu" class="dropdown-menu" style="display: none;">
                            <!-- Acceso al Panel B2B -->
                            <a href="{{ route('company.edit') }}" style="font-weight: 600; color: var(--color-tertiary);">
                                💼 Mi Espacio de Trabajo
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- Módulo B2C (Facturación y Perfil) -->
                            <a href="#" style="opacity: 0.7;" title="Próximamente">Mis Pedidos</a>
                            <a href="#" style="opacity: 0.7;" title="Próximamente">Billetera y Pagos</a>
                            <a href="#">Ajustes de Perfil</a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" style="background: none; border: none; width: 100%; text-align: left; padding: 10px 16px; color: #e53e3e; cursor: pointer; font-weight: 600;">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" style="background-color: var(--color-primary); color: white; padding: 10px 20px; text-decoration: none; border-radius: var(--radius-button); font-weight: 600;">
                        Acceso Clientes
                    </a>
                @endauth
            </div>
        </nav>
    </div>

    <!-- Contenido Inyectado mediante Blade Tradicional -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer Opcional -->
    <footer class="public-footer">
        <div class="footer-bottom">
            &copy; {{ date('Y') }} Sensoría IoT. Todos los derechos reservados.
        </div>
    </footer>

</div>

<!-- JavaScript Nativo para Interacciones del Layout -->
<script>
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
</script>

@stack('scripts')
</body>
</html>
