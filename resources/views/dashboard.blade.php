@extends('layouts.public')

@section('title', 'Panel General | Sensoría IoT')

@section('content')
    <div style="max-width: 1100px; margin: 0 auto; padding: 24px 0 60px 0;">

        <!-- Encabezado del Dashboard -->
        <div style="margin-bottom: 32px;">
        <span style="color: var(--color-primary); font-weight: 700; font-size: 0.875rem; letter-spacing: 1px; text-transform: uppercase;">
            Portal General
        </span>
            <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--color-tertiary); margin: 8px 0;">
                ¡Bienvenido de nuevo, {{ Auth::user()->name }}!
            </h1>
            <p style="color: #718096; font-size: 1.05rem; margin: 0;">
                Accede a los diferentes módulos de Sensoría desde tu panel central.
            </p>
        </div>

        <!-- Rejilla de Accesos Directos -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">

            <!-- Tarjeta Tienda / Catálogo -->
            <div class="glass-surface" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between; border-left: 4px solid var(--color-primary);">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <span style="background: rgba(88, 148, 69, 0.1); color: var(--color-primary); padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem;">
                        Módulo B
                    </span>
                    </div>
                    <h3 style="font-size: 1.35rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 8px;">
                        Catálogo B2B
                    </h3>
                    <p style="color: #718096; font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        Explora nuestros dispositivos IoT, componentes y kits modulares diseñados para optimizar tu infraestructura.
                    </p>
                </div>

                <a href="{{ Route::has('shop.products.index') ? route('shop.products.index') : '#' }}" style="margin-top: 24px; text-decoration: none; color: var(--color-primary); font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 6px;">
                    Explorar Catálogo &rarr;
                </a>
            </div>

            <!-- Tarjeta Administración Empresa (Módulo A) -->
            <div class="glass-surface" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between; border-left: 4px solid var(--color-tertiary);">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <span style="background: rgba(27, 61, 26, 0.08); color: var(--color-tertiary); padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem;">
                        Módulo A
                    </span>
                    </div>
                    <h3 style="font-size: 1.35rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 8px;">
                        Mi Organización
                    </h3>
                    <p style="color: #718096; font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        Gestiona los datos de tu empresa, configura la razón social y administra a los integrantes del equipo.
                    </p>
                </div>

                <a href="{{ route('company.edit') }}" style="margin-top: 24px; text-decoration: none; color: var(--color-primary); font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 6px;">
                    Administrar Empresa &rarr;
                </a>
            </div>

        </div>
    </div>
@endsection
