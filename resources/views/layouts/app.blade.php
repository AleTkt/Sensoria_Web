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
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}"> <!-- TOASTS -->

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body class="font-sans antialiased">
<div class="app-layout-container">

    <!-- Sidebar Oscuro -->
    <aside class="app-sidebar animate-fade-in-up"> <!-- ANIMACIÓN -->
        <a href="{{ url('/') }}" class="brand">
            Sensoría <span>IoT</span>
        </a>

        <nav class="sidebar-nav">
            <!-- Opción para regresar al sitio público -->
            <a href="{{ url('/') }}" class="nav-link-return">
                <i class="fa-solid fa-house"></i> Volver al Sitio Principal
            </a>

            @if(in_array(Auth::user()->global_role, ['ADMIN', 'SOPORTE']))
                <a href="{{ route('admin.dashboard') }}" style="color: #feb2b2; background-color: rgba(229, 62, 62, 0.1); margin-top: 8px;">
                    <i class="fa-solid fa-shield-halved"></i> Regresar al Backoffice
                </a>
            @endif

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <!-- BLOQUE 1: Módulo D (IoT y Operaciones) -->
            <span style="display: block; padding: 0 16px; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Operativa IoT</span>
            <a href="{{ route('workspace.dashboard') }}" class="{{ request()->routeIs('workspace.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard IoT
            </a>
            <a href="{{ route('workspace.devices') }}" class="{{ request()->routeIs('workspace.devices*') ? 'active' : '' }}">
                <i class="fa-solid fa-microchip"></i> Hubs y Dispositivos
            </a>
            <a href="#" class="" title="Próximamente">
                <i class="fa-solid fa-bell"></i> Centro de Alertas
            </a>

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <!-- BLOQUE 2: Módulo A (Administración) -->
            <span style="display: block; padding: 0 16px; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Organización</span>
            <a href="{{ route('company.edit') }}" class="{{ request()->routeIs('company.edit') || request()->routeIs('company.update') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i> Mi Empresa
            </a>
            <a href="{{ route('company.team') }}" class="{{ request()->routeIs('company.team*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Mi Equipo
            </a>
            <a href="{{ route('company.settings') }}" class="{{ request()->routeIs('company.settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Ajustes del Espacio
            </a>
            <a href="{{ route('company.activity') }}" class="{{ request()->routeIs('company.activity*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Bitácora de Actividad
            </a>

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <!-- BLOQUE 3: Módulos B y C (Facturación y Soporte) -->
            <span style="display: block; padding: 0 16px; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Servicios</span>
            <a href="#" class="" title="Próximamente">
                <i class="fa-solid fa-file-invoice-dollar"></i> Facturación y Pagos
            </a>
            <a href="{{ route('workspace.tickets.index') }}" class="{{ request()->routeIs('workspace.tickets.*') ? 'active' : '' }}">
                <i class="fa-solid fa-headset"></i> Soporte Técnico
            </a>
        </nav>

        <!-- Footer del Sidebar: Usuario y Cierre de Sesión -->
        <div class="sidebar-footer">
            <div style="padding: 12px 16px; background: rgba(255,255,255,0.05); border-radius: var(--radius-button); display: flex; align-items: center; justify-content: space-between;">
                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;">
                    <span style="display: block; font-weight: 600; font-size: 0.875rem; color: #ffffff;">{{ Auth::user()->name }}</span>
                    <span style="display: block; font-size: 0.75rem; color: rgba(255,255,255,0.6);">{{ Auth::user()->email }}</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="form-with-loading" style="margin: 0;"> <!-- LOADING -->
                    @csrf
                    <button type="submit" class="btn-submit-loading" title="Cerrar Sesión" style="background: none; border: none; color: #feb2b2; font-size: 1.1rem; cursor: pointer; padding: 4px; display: flex; align-items: center;">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="app-main">
        <!-- Header del Panel -->
        <header class="app-header animate-fade-in-up" style="animation-delay: 0.1s;">
            <h2 class="page-title">@yield('header', 'Panel de Administración')</h2>

            <!-- Estado de empresa activa -->
            @if(session('current_company'))
                <span style="font-size: 0.875rem; font-weight: 600; color: var(--color-primary); background: rgba(88, 148, 69, 0.1); padding: 6px 14px; border-radius: 20px;">
                    <i class="fa-solid fa-building-user"></i> {{ session('current_company')->trade_name }}
                </span>
            @endif
        </header>

        <!-- Page Content -->
        <div class="app-content animate-fade-in-up" style="animation-delay: 0.2s;">
            @yield('content')
        </div>
    </main>
</div>

<!-- Componentes Globales -->
@include('layouts.toast')

<!-- Scripts Vanilla JS -->
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/loading-buttons.js') }}"></script>
@stack('scripts')
</body>
</html>
