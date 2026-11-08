<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sensoria IoT'))</title>

    <!-- CSS Puro -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/animations.css') }}"> <!-- ANIMACIONES -->
    <link rel="stylesheet" href="{{ asset('css/layouts/public.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}"> <!-- TOASTS -->

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body>
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

            <div>
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

                            <!-- Acceso al Panel B2B -->
                            <a href="{{ route('company.edit') }}" style="font-weight: 600; color: var(--color-tertiary);">
                                <i class="fa-solid fa-building"></i>  Mi Espacio de Trabajo
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- Módulo B2C (Facturación y Perfil) -->
                            <a href="#" style="opacity: 0.7;" title="Próximamente"><i class="fa-solid fa-box"></i>  Mis Pedidos</a>
                            <a href="#" style="opacity: 0.7;" title="Próximamente"><i class="fa-solid fa-wallet"></i>  Billetera y Pagos</a>
                            <a href="#"><i class="fa-solid fa-user-gear"></i>  Ajustes de Perfil</a>

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

</div>

<!-- Componentes Globales -->
@include('layouts.toast')

<!-- JavaScript Nativo -->
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/loading-buttons.js') }}"></script>
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
