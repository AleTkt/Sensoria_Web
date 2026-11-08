@extends('layouts.public')

@section('title', 'Panel General | Sensoría IoT')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
@endpush

@section('content')
    <div class="dashboard-container">

        <!-- Encabezado del Dashboard -->
        <div class="dashboard-header animate-fade-in-up">
            <span class="dashboard-subtitle">
                <i class="fa-solid fa-border-all"></i> Portal General
            </span>
            <h1 class="dashboard-title">
                ¡Bienvenido de nuevo, {{ Auth::user()->name }}!
            </h1>
            <p class="dashboard-desc">
                Accede a los diferentes módulos de Sensoría desde tu panel central. Selecciona el área de trabajo a la que deseas dirigirte.
            </p>
        </div>

        <!-- Rejilla de Accesos Directos -->
        <div class="dashboard-grid animate-fade-in-up" style="animation-delay: 0.1s;">

            <!-- Tarjeta Tienda / Catálogo (Módulo B) -->
            <a href="{{ Route::has('shop.products.index') ? route('shop.products.index') : '#' }}" class="glass-surface dashboard-card card-module-b">
                <div>
                    <div class="card-header-flex">
                        <span class="card-badge badge-b">Módulo B</span>
                        <i class="fa-solid fa-box-open card-icon icon-b"></i>
                    </div>
                    <h3 class="card-title">Catálogo B2B</h3>
                    <p class="card-text">
                        Explora nuestros dispositivos IoT, componentes y kits modulares diseñados para optimizar tu infraestructura tecnológica.
                    </p>
                </div>

                <div class="card-action action-b">
                    Explorar Catálogo <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- Tarjeta Administración Empresa (Módulo A) -->
            <a href="{{ route('company.edit') }}" class="glass-surface dashboard-card card-module-a">
                <div>
                    <div class="card-header-flex">
                        <span class="card-badge badge-a">Módulo A</span>
                        <i class="fa-solid fa-building-user card-icon icon-a"></i>
                    </div>
                    <h3 class="card-title">Mi Organización</h3>
                    <p class="card-text">
                        Gestiona los datos de tu empresa, configura la razón social, administra el espacio de trabajo y coordina a los integrantes de tu equipo.
                    </p>
                </div>

                <div class="card-action action-a">
                    Administrar Empresa <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

        </div>
    </div>
@endsection
