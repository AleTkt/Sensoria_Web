<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sensoria IoT'))</title>

    <!-- CSS Puro -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">

    @stack('styles')
</head>
<body class="font-sans antialiased">
<div class="app-layout-container">

    <!-- Sidebar Oscuro -->
    <aside class="app-sidebar">
        <a href="{{ url('/') }}" class="brand">
            Sensoría <span>IoT</span>
        </a>

        <nav class="sidebar-nav">
            <!-- Opción para regresar al sitio público -->
            <a href="{{ url('/') }}" class="nav-link-return">
                <i class="icon-home"></i> Volver al Sitio Principal
            </a>

            @if(in_array(Auth::user()->global_role, ['ADMIN', 'SOPORTE']))
                <a href="{{ route('admin.dashboard') }}" style="color: #feb2b2; background-color: rgba(229, 62, 62, 0.1); margin-top: 8px;">
                    Regresar al Backoffice
                </a>
            @endif

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <!-- BLOQUE 1: Módulo D (IoT y Operaciones) - Próximamente -->
            <span style="display: block; padding: 0 16px; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Operativa IoT</span>
            <a href="#" class="" style="opacity: 0.7;" title="Próximamente">
                <i class="icon-dashboard"></i> Dashboard IoT
            </a>
            <a href="#" class="" style="opacity: 0.7;" title="Próximamente">
                <i class="icon-device"></i> Hubs y Dispositivos
            </a>
            <a href="#" class="" style="opacity: 0.7;" title="Próximamente">
                <i class="icon-alert"></i> Centro de Alertas
            </a>

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <!-- BLOQUE 2: Módulo A (Administración) -->
            <span style="display: block; padding: 0 16px; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Organización</span>
            <a href="{{ route('company.edit') }}" class="{{ request()->routeIs('company.edit') || request()->routeIs('company.update') ? 'active' : '' }}">
                <i class="icon-company"></i> Mi Empresa
            </a>
            <a href="{{ route('company.team') }}" class="{{ request()->routeIs('company.team*') ? 'active' : '' }}">
                <i class="icon-team"></i> Mi Equipo
            </a>
            <a href="{{ route('company.settings') }}" class="{{ request()->routeIs('company.settings*') ? 'active' : '' }}">
                <i class="icon-settings"></i> Ajustes del Espacio
            </a>
            <a href="{{ route('company.activity') }}" class="{{ request()->routeIs('company.activity*') ? 'active' : '' }}">
                <i class="icon-activity"></i> Bitácora de Actividad
            </a>

            <div style="height: 1px; background: rgba(255,255,255,0.08); margin: 12px 0;"></div>

            <!-- BLOQUE 3: Módulos B y C (Facturación y Soporte) - Próximamente -->
            <span style="display: block; padding: 0 16px; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Servicios</span>
            <a href="#" class="" style="opacity: 0.7;" title="Próximamente">
                <i class="icon-billing"></i> Facturación y Pagos
            </a>
            <a href="#" class="" style="opacity: 0.7;" title="Próximamente">
                <i class="icon-support"></i> Soporte Técnico
            </a>
        </nav>

        <!-- Footer del Sidebar: Usuario y Cierre de Sesión -->
        <div class="sidebar-footer">
            <div style="padding: 12px 16px; background: rgba(255,255,255,0.05); border-radius: var(--radius-button); display: flex; align-items: center; justify-content: space-between;">
                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;">
                    <span style="display: block; font-weight: 600; font-size: 0.875rem; color: #ffffff;">{{ Auth::user()->name }}</span>
                    <span style="display: block; font-size: 0.75rem; color: rgba(255,255,255,0.6);">{{ Auth::user()->email }}</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Cerrar Sesión" style="background: none; border: none; color: #feb2b2; font-size: 1.1rem; cursor: pointer; padding: 4px; display: flex; align-items: center;">
                        <i class="icon-logout"> Cerrar sesión </i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="app-main">
        <!-- Header del Panel -->
        <header class="app-header">
            <h2 class="page-title">@yield('header', 'Panel de Administración')</h2>

            <!-- Estado de empresa activa -->
            @if(session('current_company'))
                <span style="font-size: 0.875rem; font-weight: 600; color: var(--color-primary); background: rgba(88, 148, 69, 0.1); padding: 6px 14px; border-radius: 20px;">
                    {{ session('current_company')->trade_name }}
                </span>
            @endif
        </header>

        <!-- Toast de Notificaciones Flash (Éxito / Error) -->
        @if(session('success') || session('error') || session('warning'))
            <div style="padding: 16px 32px 0 32px;">
                @if(session('success'))
                    <div style="background-color: rgba(88, 148, 69, 0.15); border: 1px solid var(--color-primary); color: var(--color-tertiary); padding: 12px 16px; border-radius: var(--radius-button); font-weight: 600; font-size: 0.9rem;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background-color: rgba(229, 62, 62, 0.15); border: 1px solid #e53e3e; color: #9b2c2c; padding: 12px 16px; border-radius: var(--radius-button); font-weight: 600; font-size: 0.9rem;">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('warning'))
                    <div style="background-color: rgba(221, 107, 32, 0.15); border: 1px solid #dd6b20; color: #9c4221; padding: 12px 16px; border-radius: var(--radius-button); font-weight: 600; font-size: 0.9rem;">
                        {{ session('warning') }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Page Content -->
        <div class="app-content">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
