@extends('layouts.public')

@section('title', 'Mis Compras y Códigos de Activación - Sensoría IoT')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/client/orders/mis-compras.css') }}">
@endpush

@section('content')
    <div class="orders-page-wrapper">

        <!-- HEADER DE LA PÁGINA -->
        <div class="orders-header">
            <div>
                <h2 class="orders-title">
                    <i class="fa-solid fa-boxes-packing" style="color: var(--color-primary);"></i>
                    Mis Compras & Códigos de Activación
                </h2>
                <p class="orders-subtitle">
                    Consulte el historial de sus adquisiciones de kits IoT, estados de pago y códigos de enlace para sus hubs.
                </p>
            </div>
            @if(!$orders->isEmpty())
                <span class="orders-count-badge">
                    <i class="fa-solid fa-receipt"></i> {{ $orders->total() }} {{ $orders->total() === 1 ? 'Compra' : 'Compras' }}
                </span>
            @endif
        </div>

        <!-- Alerta de Éxito al venir del Checkout -->
        @if (session('success'))
            <div style="margin-bottom: 1.5rem; background-color: rgba(88, 148, 69, 0.12); border: 1px solid var(--color-primary); color: var(--color-tertiary); padding: 1rem 1.25rem; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;" role="alert">
                <i class="fa-solid fa-circle-check" style="font-size: 1.3rem; color: var(--color-primary);"></i>
                <div>
                    <strong style="font-weight: 700; display: block;">¡Operación realizada con éxito!</strong>
                    <span style="font-size: 0.9rem; font-weight: 500;">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- TARJETA PRINCIPAL -->
        <div class="orders-card-container">

            @if($orders->isEmpty())
                <div class="empty-orders-state">
                    <i class="fa-solid fa-basket-shopping empty-orders-icon"></i>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--color-tertiary); margin-bottom: 0.5rem;">No tiene compras registradas aún</h3>
                    <p style="font-size: 0.9rem; max-width: 420px; margin: 0 auto 1.5rem auto; color: #6b7280;">
                        Explore nuestro catálogo de soluciones modulares e impulse la transformación de su empresa con Sensoría.
                    </p>
                    <a href="{{ route('shop.products.index') }}" class="btn-pdf-download" style="padding: 0.75rem 1.5rem; font-size: 0.9rem; background-color: var(--color-primary);">
                        <i class="fa-solid fa-store"></i> Explorar Catálogo IoT
                    </a>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="orders-table">
                        <thead>
                        <tr>
                            <th># Orden</th>
                            <th>Fecha de Compra</th>
                            <th>Monto Total</th>
                            <th>Estado</th>
                            <th style="text-align: center;">Código de Activación</th>
                            <th style="text-align: center;">Comprobante</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('client.orders.show', $order->id) }}" class="order-number-link" title="Ver detalle de la compra">
                                        <i class="fa-solid fa-hashtag" style="font-size: 0.75rem; color: var(--color-primary);"></i>
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td style="color: #4b5563;">
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        <i class="fa-regular fa-calendar-alt" style="color: #9ca3af; font-size: 0.85rem;"></i>
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td style="font-weight: 800; color: var(--color-tertiary); font-size: 1rem;">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = match(strtoupper($order->status)) {
                                            'PAGADO' => 'status-pagado',
                                            'PENDIENTE' => 'status-pendiente',
                                            default => 'status-cancelado',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    @if($order->activationCode)
                                        <div style="display: inline-flex; align-items: center; justify-content: center;">
                                            <span class="activation-code-pill">
                                                {{ $order->activationCode->code }}
                                            </span>
                                            <span class="activation-status-sub">
                                                ({{ $order->activationCode->status }})
                                            </span>
                                        </div>
                                    @else
                                        <span style="color: #9ca3af; font-size: 0.8rem; font-style: italic;">No disponible</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('client.orders.pdf', $order->id) }}" class="btn-pdf-download" title="Descargar comprobante en PDF">
                                        <i class="fa-solid fa-file-pdf" style="color: #f87171;"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                @if($orders->hasPages())
                    <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
@endsection
