@extends('layouts.public')

@section('title', 'Billetera y Pagos | Sensoría')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/shop/product-detail.css') }}">
@endpush

@section('content')
    <div class="shop-page-wrapper">
        <div class="shop-container">

            <!-- Enlace de regreso -->
            <a href="{{ route('shop.products.index') }}" class="back-link">
                ← Volver a la Tienda
            </a>

            <!-- Encabezado de la Sección -->
            <div class="shop-header" style="margin-bottom: 2rem;">
                <h1 class="page-title">Mi Billetera & Historial de Pagos</h1>
                <p class="page-subtitle">Gestione sus métodos de pago simulados y supervise las transacciones de sus kits IoT.</p>
            </div>

            <!-- Tarjeta de Saldo / Resumen (Glassmorphism) -->
            <div class="product-card glass-surface" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                <div>
                    <span style="font-size: 0.875rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Saldo Disponible en Cuenta</span>
                    <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--color-primary); margin: 0.25rem 0 0 0;">$0.00</h2>
                </div>
                <div>
                    <button type="button" class="btn-sensoria" onclick="alert('Módulo de recarga simulado próximamente.');">
                        <i class="fa-solid fa-plus-circle"></i> Recargar Fondos
                    </button>
                </div>
            </div>

            <!-- Tabla de Transacciones / Historial Vacío -->
            <div class="product-card glass-surface" style="padding: 3rem 2rem; text-align: center;">
                <i class="fa-solid fa-wallet" style="font-size: 3rem; color: var(--color-tertiary); opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                <h3 class="card-title">No hay transacciones registradas</h3>
                <p class="card-desc" style="max-width: 400px; margin: 0 auto 1.5rem auto;">
                    Cuando adquiera kits modulares o servicios de soporte en Sensoría, el detalle de sus pagos figurará en este espacio.
                </p>
                <a href="{{ route('shop.products.index') }}" class="btn-sensoria" style="display: inline-flex;">
                    Explorar Catálogo IoT
                </a>
            </div>

        </div>
    </div>
@endsection
