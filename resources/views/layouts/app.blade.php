    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sensoría') }}</title>

        <!-- Enlace a los colores y fuentes corporativas -->
        <link rel="stylesheet" href="{{ asset('css/base/variables.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="antialiased min-h-screen flex flex-col md:flex-row p-4 md:p-6 gap-6">

    <!-- Menú Lateral Cristalino -->
    @include('layouts.navigation')

    <!-- Área Central de Contenido -->
    <div class="flex-1 flex flex-col min-w-0">
        @isset($header)
            <header class="mb-6">
                <h1 class="text-2xl font-bold" style="color: var(--color-tertiary);">
                    {{ $header }}
                </h1>
            </header>
        @endisset

        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
    </body>
    </html>
