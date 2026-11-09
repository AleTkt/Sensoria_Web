@extends('layouts.app')

@section('title', 'Dashboard IoT | Sensoría')
@section('header', 'Métricas Operativas')

@section('content')
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

        <!-- Tarjeta Estándar -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Sensores Activos</h3>
            <div style="font-size: 36px; font-weight: 700; margin: 12px 0; color: #0f172a;">
                {{ $onlineDevices }} <span style="font-size: 18px; font-weight: 500; color: #94a3b8;">/ {{ $totalDevices }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $onlineDevices > 0 ? '#10b981' : '#ef4444' }};"></span>
                <span style="font-size: 13px; color: #64748b;">
                {{ $onlineDevices > 0 ? 'Telemetría estable' : 'Requiere revisión' }}
            </span>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Gateways (Hubs)</h3>
            <div style="font-size: 36px; font-weight: 700; margin: 12px 0; color: #0f172a;">{{ $hubsCount }}</div>
            <div style="font-size: 13px; color: #64748b;">Concentradores en operación</div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Tickets Abiertos</h3>
            <div style="font-size: 36px; font-weight: 700; margin: 12px 0; color: #0f172a;">{{ $openTicketsCount }}</div>
            <div style="font-size: 13px; color: #64748b;">Casos de soporte B2B</div>
        </div>
    </div>
@endsection
