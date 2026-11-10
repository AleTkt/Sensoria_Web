@extends('layouts.backoffice')

@section('title', 'Pedidos Globales - Staff Sensoría')

@section('content')
    <div style="padding: 1.5rem;">

        <!-- ENCABEZADO DE SECCIÓN -->
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #1B3D1A; margin: 0;">
                <i class="fa-solid fa-boxes-stacked" style="color: #589445;"></i> Pedidos Globales
            </h1>
            <p style="font-size: 0.9rem; color: #6b7280; margin-top: 0.25rem;">
                Monitoreo y auditoría de todas las compras de kits IoT y soluciones realizadas por las empresas asociadas.
            </p>
        </div>

        <!-- TARJETAS DE DASHBOARD DE PEDIDOS -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 2rem;">

            <!-- Total de Pedidos -->
            <div style="background: white; padding: 20px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h4 style="margin: 0; color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Pedidos</h4>
                <p style="margin: 10px 0 0 0; font-size: 2rem; font-weight: 700; color: #0f172a;">{{ $stats['total_orders'] ?? 0 }}</p>
            </div>

            <!-- Ingresos Totales (Pagados) -->
            <div style="background: white; padding: 20px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h4 style="margin: 0; color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Ingresos (Pagados)</h4>
                <p style="margin: 10px 0 0 0; font-size: 2rem; font-weight: 700; color: #16a34a;">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
            </div>

            <!-- Pedidos Pagados -->
            <div style="background: white; padding: 20px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h4 style="margin: 0; color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Órdenes Pagadas</h4>
                <p style="margin: 10px 0 0 0; font-size: 2rem; font-weight: 700; color: #0f172a;">{{ $stats['paid_orders'] ?? 0 }}</p>
            </div>

            <!-- Pedidos Pendientes -->
            <div style="background: white; padding: 20px; border: 1px solid var(--admin-border, #e2e8f0); border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h4 style="margin: 0; color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Órdenes Pendientes</h4>
                <p style="margin: 10px 0 0 0; font-size: 2rem; font-weight: 700; color: #d97706;">{{ $stats['pending_orders'] ?? 0 }}</p>
            </div>

        </div>

        <!-- SECCIÓN DE FILTROS RÁPIDOS (DISEÑO HORIZONTAL LIMPIO) -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.08); padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('admin.orders.index') }}" style="display: grid; grid-template-columns: minmax(240px, 2fr) 180px 150px 150px auto; gap: 1rem; align-items: end;">

                <!-- Buscador de texto -->
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #4b5563; text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.03em;">Búsqueda</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº Orden o Empresa..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; box-sizing: border-box; background: #fafaf9;">
                </div>

                <!-- Selector de Estado -->
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #4b5563; text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.03em;">Estado</label>
                    <select name="status" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; background: #fafaf9; box-sizing: border-box;">
                        <option value="">Todos los estados</option>
                        <option value="PAGADO" {{ request('status') === 'PAGADO' ? 'selected' : '' }}>PAGADO</option>
                        <option value="PENDIENTE" {{ request('status') === 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
                        <option value="CANCELADO" {{ request('status') === 'CANCELADO' ? 'selected' : '' }}>CANCELADO</option>
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #4b5563; text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.03em;">Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; box-sizing: border-box; background: #fafaf9;">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #4b5563; text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.03em;">Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; box-sizing: border-box; background: #fafaf9;">
                </div>

                <!-- Botones de Acción -->
                <div style="display: flex; gap: 0.5rem; height: 38px;">
                    <button type="submit" style="background-color: #1B3D1A; color: white; border: none; padding: 0 1rem; border-radius: 8px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; font-size: 0.88rem; height: 100%; transition: background-color 0.2s;" title="Aplicar filtros">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.orders.index') }}" style="background-color: #f3f4f6; color: #4b5563; padding: 0 0.85rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; font-size: 0.88rem; height: 100%; border: 1px solid #e5e7eb;" title="Limpiar filtros">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>
        <!-- TABLA CONTENEDORA -->
        <div style="background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.08); padding: 1.5rem;">

            @if($orders->isEmpty())
                <div style="text-align: center; padding: 3rem 1rem; color: #6b7280;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 3rem; opacity: 0.25; color: #1B3D1A; margin-bottom: 1rem; display: block;"></i>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #1B3D1A;">No se encontraron pedidos con los filtros especificados</h3>
                    <p style="font-size: 0.85rem; margin-top: 0.5rem;">Pruebe ajustando los criterios de búsqueda o limpiando los filtros.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse; min-width: 900px;">
                        <thead>
                        <tr style="background-color: #f9faf7; border-bottom: 2px solid rgba(88, 148, 69, 0.2); color: #1B3D1A; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem; font-weight: 700;"># Orden</th>
                            <th style="padding: 1rem; font-weight: 700;">Empresa (PYME)</th>
                            <th style="padding: 1rem; font-weight: 700;">Fecha</th>
                            <th style="padding: 1rem; font-weight: 700; text-align: right;">Total</th>
                            <th style="padding: 1rem; font-weight: 700; text-align: center;">Estado</th>
                            <th style="padding: 1rem; font-weight: 700; text-align: center;">Código Activación</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem; font-weight: 700; color: #1B3D1A;">
                                    {{ $order->order_number }}
                                </td>
                                <td style="padding: 1rem; color: #374151;">
                                    <strong>{{ $order->company->trade_name ?? 'N/A' }}</strong>
                                </td>
                                <td style="padding: 1rem; color: #6b7280; font-size: 0.875rem;">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td style="padding: 1rem; font-weight: 800; color: #1B3D1A; text-align: right; font-size: 1rem;">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @php
                                        $statusClass = match(strtoupper($order->status)) {
                                            'PAGADO' => 'background-color: #d1fae5; color: #065f46;',
                                            'PENDIENTE' => 'background-color: #fef3c7; color: #92400e;',
                                            default => 'background-color: #fee2e2; color: #991b1b;',
                                        };
                                    @endphp
                                    <span style="{{ $statusClass }} padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($order->activationCode)
                                        <span style="font-family: monospace; font-weight: 700; color: #047857; background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.35rem 0.75rem; border-radius: 4px; font-size: 0.85rem;">
                                            {{ $order->activationCode->code }}
                                        </span>
                                    @else
                                        <span style="color: #9ca3af; font-size: 0.75rem; font-style: italic;">Sin código</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN CON QUERIES ACTIVAS -->
                @if($orders->hasPages())
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
@endsection
