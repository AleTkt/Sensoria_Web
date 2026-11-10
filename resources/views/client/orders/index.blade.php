@extends('layouts.public')

@section('title', 'Mis Compras y Códigos de Activación - Sensoría IoT')

@section('content')
    <div style="padding: 3rem 1rem; max-width: 1200px; margin: 0 auto; min-height: 70vh;">

        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--color-primary); margin-bottom: 2rem; border-bottom: 2px solid var(--color-tertiary); padding-bottom: 0.5rem;">
            Mis Compras y Códigos de Activación
        </h2>

        <!-- Alerta de Éxito al venir del Checkout -->
        @if (session('success'))
            <div style="margin-bottom: 1.5rem; background-color: #d1fae5; border: 1px solid #34d399; color: #065f46; padding: 1rem; border-radius: var(--radius-card);" role="alert">
                <strong style="font-weight: 700;">¡Éxito!</strong>
                <span style="display: block; sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div style="background: white; border-radius: var(--radius-card); box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 1.5rem;">

            @if($orders->isEmpty())
                <div style="text-align: center; padding: 3rem 0; color: #6b7280;">
                    No tienes compras registradas aún.
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse; min-width: 800px;">
                        <thead style="background-color: #f9fafb; border-bottom: 2px solid var(--color-primary);">
                        <tr>
                            <th style="padding: 1rem; font-weight: 600; color: var(--color-primary);"># Orden</th>
                            <th style="padding: 1rem; font-weight: 600; color: var(--color-primary);">Fecha</th>
                            <th style="padding: 1rem; font-weight: 600; color: var(--color-primary);">Total</th>
                            <th style="padding: 1rem; font-weight: 600; color: var(--color-primary);">Estado</th>
                            <th style="padding: 1rem; font-weight: 600; color: var(--color-primary); text-align: center;">Código de Activación</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem; font-weight: 600;">
                                    {{ $order->order_number }}
                                </td>
                                <td style="padding: 1rem; color: #4b5563;">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td style="padding: 1rem; font-weight: 700;">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td style="padding: 1rem;">
                                    <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($order->activationCode)
                                        <span style="font-family: monospace; font-weight: 700; color: #047857; background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.4rem 0.8rem; border-radius: 4px; letter-spacing: 1px;">
                                            {{ $order->activationCode->code }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: #6b7280; margin-left: 0.5rem; text-transform: uppercase;">
                                            ({{ $order->activationCode->status }})
                                        </span>
                                    @else
                                        <span style="color: #9ca3af; font-size: 0.75rem;">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1.5rem;">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
