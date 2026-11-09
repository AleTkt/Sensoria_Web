@extends('layouts.app')

@section('title', 'Métricas del Sensor | Sensoría')
@section('header', 'Telemetría: ' . $device->name)

@section('content')
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Tipo: {{ $device->type }}</span>
                <h2 style="margin: 4px 0 0 0; font-size: 1.25rem; color: #0f172a;">ID del Hardware: {{ $device->id }}</h2>
            </div>
            <a href="{{ route('workspace.devices') }}" style="padding: 8px 16px; background: #f1f5f9; color: #475569; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i> Volver a Dispositivos
            </a>
        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; font-size: 1.05rem; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">Historial de Registros</h3>

        <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
            <thead>
            <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #64748b; font-size: 0.85rem;">
                <th style="padding: 10px;">Valor Registrado</th>
                <th style="padding: 10px;">Unidad</th>
                <th style="padding: 10px;">Marca de Tiempo</th>
            </tr>
            </thead>
            <tbody>
            @forelse($readings as $reading)
                <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                    <td style="padding: 10px; font-weight: 700; color: #0f172a;">{{ $reading->value }}</td>
                    <td style="padding: 10px; color: #64748b;">{{ $reading->unit ?? 'N/A' }}</td>
                    <td style="padding: 10px; color: #64748b;">{{ $reading->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 24px; color: #94a3b8;">No hay registros de telemetría para este dispositivo.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
