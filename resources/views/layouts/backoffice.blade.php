<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sensoría Backoffice')</title>

    <!-- CSS Base y Específico del Admin -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/admin-layout.css') }}">

    <!-- DataTables CSS (Estilo Base Puro) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    @stack('styles')
</head>
<body class="admin-body">

<!-- Barra superior de seguridad -->
<div class="admin-top-banner">
    MODO STAFF SENSORÍA
</div>

<div class="admin-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <h2>Sensoría <span>STAFF</span></h2>
        </div>

        <nav class="admin-nav">
            <!-- Plataforma -->
            <div class="nav-section">
                <h3>Plataforma</h3>
                <a href="{{ url('/admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="icon-dashboard"></i> Panel Principal
                </a>
                <a href="{{ url('/admin/empresas') }}" class="{{ request()->is('admin/empresas*') ? 'active' : '' }}">
                    <i class="icon-company"></i> Empresas (Tenants)
                </a>
                <a href="{{ url('/admin/usuarios') }}" class="{{ request()->is('admin/usuarios*') ? 'active' : '' }}">
                    <i class="icon-team"></i> Usuarios Globales
                </a>
            </div>

            <!-- Catálogo (Módulo B) -->
            <div class="nav-section">
                <h3>Catálogo y Hardware</h3>
                <a href="{{ url('/admin/catalogo/kits') }}" class="{{ request()->is('admin/catalogo/kits*') ? 'active' : '' }}">
                    <i class="icon-kit"></i> Kits Comerciales
                </a>
                <a href="{{ url('/admin/catalogo/hardware') }}" class="{{ request()->is('admin/catalogo/hardware*') ? 'active' : '' }}">
                    <i class="icon-device"></i> Hardware Base
                </a>
                <a href="{{ url('/admin/catalogo/categorias') }}" class="{{ request()->is('admin/catalogo/categorias*') ? 'active' : '' }}">
                    <i class="icon-category"></i> Categorías
                </a>
            </div>

            <!-- Logística y Finanzas -->
            <div class="nav-section">
                <h3>Logística y Finanzas</h3>
                <a href="{{ url('/admin/pedidos') }}" class="{{ request()->is('admin/pedidos*') ? 'active' : '' }}">
                    <i class="icon-orders"></i> Pedidos Globales
                </a>
                <a href="{{ url('/admin/facturacion') }}" class="{{ request()->is('admin/facturacion*') ? 'active' : '' }}">
                    <i class="icon-billing"></i> Facturación
                </a>
            </div>

            <!--  Soporte y Auditoría -->
            <div class="nav-section">
                <h3>Soporte y Auditoría</h3>
                <a href="{{ url('/admin/tickets') }}" class="{{ request()->is('admin/tickets*') ? 'active' : '' }}">
                    <i class="icon-support"></i> Tickets Globales
                </a>
                <a href="{{ url('/admin/bitacora') }}" class="{{ request()->is('admin/bitacora*') ? 'active' : '' }}">
                    <i class="icon-activity"></i> Auditoría de Sistema
                </a>
            </div>
        </nav>

        <!-- Footer Sidebar con Info de Usuario -->
        <div class="admin-user-footer">
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                <span class="user-role">{{ Auth::user()->global_role ?? 'STAFF' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout" title="Cerrar Sesión">
                    <i class="icon-logout"> Salir</i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-titles">
                <h1>@yield('header', 'Panel de Administración')</h1>
            </div>
            <div class="header-actions">
                @yield('header-actions')
            </div>
        </header>

        <div class="admin-content">
            @yield('content')
        </div>
    </main>
</div>

<!-- Scripts Base (jQuery y DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<!-- Configuración Global por Defecto para DataTables al Español -->
<script>
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 15,
        lengthMenu: [15, 25, 50, 100],
        responsive: true
    });
</script>

@stack('scripts')
</body>
</html>
