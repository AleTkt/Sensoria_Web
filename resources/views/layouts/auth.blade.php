<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sensoria IoT'))</title>

    <!-- CSS Puro -->
    <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/public.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/animations.css') }}">


    @stack('styles')
</head>
<body>
<div class="auth-layout-container" style="min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px;">

    <!-- Botón Volver al Inicio -->
    <div style="width: 100%; max-width: 520px; margin-bottom: 16px;">
        <a href="{{ url('/') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-tertiary); text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            <i class="icon-arrow-left"></i> &larr; Volver al inicio
        </a>
    </div>

    <!-- Contenido de Autenticación (Login / Register) -->
    <main style="width: 100%;">
        @yield('content')
    </main>

</div>

@stack('scripts')
@include('layouts.toast')
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/loading-buttons.js') }}"></script>
</body>
</html>
