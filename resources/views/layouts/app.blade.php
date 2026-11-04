<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sensoria IoT') }}</title>

    <!-- CSS Puro -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">

    <!-- Alpine.js para interactividad (Modales y Dropdowns) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
<div class="app-layout-container">

    <!-- Sidebar -->
    <aside class="app-sidebar">
        <a href="{{ url('/') }}" class="brand">
            Sensoría <span>IoT</span>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ route('company.edit') }}" class="{{ request()->routeIs('company.*') && !request()->routeIs('company.team') ? 'active' : '' }}">
                Mi Empresa
            </a>
            <a href="{{ route('company.team') }}" class="{{ request()->routeIs('company.team*') ? 'active' : '' }}">
                Mi Equipo
            </a>
        </nav>

        <div class="sidebar-footer">
            <!-- Dropdown de usuario o botón de salida -->
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="app-main">
        <!-- Header -->
        @isset($header)
            <header class="app-header">
                <h2 class="page-title">{{ $header }}</h2>
            </header>
        @endisset

        <!-- Page Content -->
        <div class="app-content">
            {{ $slot }}
        </div>
    </main>
</div>
</body>
</html>
