@extends('layouts.app')

@section('title', 'Sensores IoT | Sensoría')
@section('header', 'Dispositivos y Métrica de Hubs')

@section('content')

    {{-- Nuevo bloque: Formulario de Canje de Código --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
        <h2 style="margin-top: 0; font-size: 1.1rem; color: #0f172a;">Activar Nuevo Kit / Hub</h2>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
            Ingresa el código de activación que viene en la caja de tu kit Sensoría para enlazar el hardware a tu empresa.
        </p>

        <form action="{{ route('workspace.devices.redeem') }}" method="POST" style="display: flex; gap: 12px;">
            @csrf
            <input type="text" name="activation_code" placeholder="Ej: SNSR-1234-ABCD" required
                   style="flex: 1; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none;">

            <button type="submit" style="background: #589445; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                Canjear Código
            </button>
        </form>

        {{-- Mensajes de validación y éxito --}}
        @error('activation_code')
        <div style="color: #ef4444; font-size: 0.85rem; margin-top: 12px; font-weight: 600;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
        </div>
        @enderror
        @if(session('success'))
            <div style="color: #10b981; font-size: 0.85rem; margin-top: 12px; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="color: #ef4444; font-size: 0.85rem; margin-top: 12px; font-weight: 600;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Tabla original de dispositivos --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h2 style="margin-top: 0; font-size: 1.1rem; color: #0f172a;">Flota de Hardware Conectada</h2>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
            <thead>
            <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #64748b; font-size: 0.85rem;">
                <th style="padding: 12px;">Nombre del Dispositivo</th>
                <th style="padding: 12px;">Tipo</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px;">Última Lectura</th>
            </tr>
            </thead>
            <tbody>
            @forelse($devices as $device)
                <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                    <td style="padding: 12px; font-weight: 600;">
                        <a href="{{ route('workspace.devices.metrics', $device->id) }}" style="color: #589445; text-decoration: none;">
                            {{ $device->name }} <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
                        </a>
                    </td>
                    <td style="padding: 12px; color: #64748b;">{{ $device->type }}</td>
                    <td style="padding: 12px;">
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; background: {{ ($device->is_active ?? true) ? 'rgba(16, 185, 129, 0.1); color: #10b981;' : 'rgba(239, 68, 68, 0.1); color: #ef4444;' }}">
                            {{ ($device->is_active ?? true) ? 'EN LÍNEA' : 'INACTIVO' }}
                        </span>
                    </td>
                    <td style="padding: 12px; color: #64748b;">{{ $device->last_reading ?? 'Sin datos' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 32px; color: #94a3b8;">No hay dispositivos registrados en los hubs de esta empresa.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
