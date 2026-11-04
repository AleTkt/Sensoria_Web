<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sensoría IoT')</title>

    <!-- Enlace directo al CSS puro sin Vite -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/public.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body>
<div class="public-layout-container">

    <!-- Navbar Flotante Glassmorphism -->
    <header class="navbar-wrapper">
        <nav class="navbar">
            <div class="brand">
                <a href="{{ url('/') }}" style="text-decoration: none;">
                    <h1>Sensoria <span>IoT</span></h1>
                </a>
            </div>

            <ul class="nav-links">
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ url('/tienda') }}" class="{{ request()->is('tienda*') ? 'active' : '' }}">Kits Modulares</a></li>
            </ul>

            <div class="actions">
                @auth
                    <!-- Usuario Logueado: Menú Desplegable con Alpine.js -->
                    <div x-data="{ open: false }" class="relative" @click.away="open = false" style="position: relative;">

                        <!-- Botón para abrir el menú -->
                        <button @click="open = !open" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; color: var(--color-tertiary); font-weight: 600;">
                            <div style="display: flex; flex-direction: column; text-align: right; line-height: 1.2;">
                                <span>{{ Auth::user()->name }}</span>
                                @php
                                    $currentCompany = session()->has('current_company_id') ? Auth::user()->companies()->find(session('current_company_id')) : null;
                                @endphp
                                @if($currentCompany)
                                    <span style="font-size: 0.75rem; color: var(--color-primary);">🏢 {{ $currentCompany->trade_name }}</span>
                                @endif
                            </div>
                            <!-- Icono de flecha hacia abajo -->
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Contenido del Dropdown -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             style="position: absolute; top: 100%; right: 0; margin-top: 10px; width: 220px; background-color: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05); overflow: hidden; z-index: 50; display: none;">

                            <a href="{{ route('dashboard') }}" style="display: block; padding: 12px 16px; color: var(--color-tertiary); text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid rgba(0,0,0,0.05); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(88, 148, 69, 0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                Panel Administrativo
                            </a>

                            <a href="{{ route('profile.edit') }}" style="display: block; padding: 12px 16px; color: var(--color-tertiary); text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid rgba(0,0,0,0.05); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(88, 148, 69, 0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                Mi Perfil
                            </a>

                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" style="display: block; padding: 12px 16px; color: #E53E3E; text-decoration: none; font-size: 0.9rem; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(229, 62, 62, 0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                                    Cerrar Sesión
                                </a>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Invitado -->
                    <a href="{{ route('login') }}" class="btn-primary" style="background-color: var(--color-primary); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600;">
                        Acceso Clientes
                    </a>
                @endauth
            </div>
        </nav>
    </header>
    <!-- Contenedor Principal -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="public-footer">
        <div class="footer-content">
            <div class="brand-col">
                <h2>Sensoria <span>IoT</span></h2>
                <p>Soluciones de automatización y telemetría accesibles para la industria y PYMES.</p>
            </div>
            <div class="links-col">
                <h4>Plataforma</h4>
                <ul>
                    <li><a href="{{ url('/tienda') }}">Kits Modulares</a></li>
                    <li><a href="#">Casos de Éxito</a></li>
                    <li><a href="#">Documentación API</a></li>
                </ul>
            </div>
            <div class="links-col">
                <h4>Soporte</h4>
                <ul>
                    <li><a href="#">Centro de Ayuda</a></li>
                    <li><a href="#">Estado del Sistema</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Sensoria IoT. Todos los derechos reservados.</p>
        </div>
    </footer>
</div>
</body>
</html>
