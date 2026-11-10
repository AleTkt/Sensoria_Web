@extends('layouts.app')

@section('title', 'Sensores IoT | Sensoría')
@section('header', 'Dispositivos y Métrica de Hubs')

@section('content')

    {{-- Bloque: Formulario de Canje de Código --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
        <h2 style="margin-top: 0; font-size: 1.1rem; color: #0f172a;">Activar Nuevo Kit / Hub</h2>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
            Ingresa el código de activación que viene en la caja de tu kit Sensoría para enlazar el hardware a tu empresa.
        </p>

        <form action="{{ route('workspace.devices.redeem') }}" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap;">
            @csrf
            <input type="text" name="activation_code" placeholder="Ej: SNSR-1234-ABCD" required
                   style="flex: 1; min-width: 250px; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none;">

            <button type="submit" style="background: #589445; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                Canjear Código
            </button>
        </form>

        {{-- Mensajes de validación --}}
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

    {{-- Nuevo Bloque: Grid de Tarjetas (Cards) para Dispositivos --}}
    <h2 style="font-size: 1.1rem; color: #0f172a; margin-bottom: 16px;">Flota de Hardware Conectada</h2>

    @if($devices->isEmpty())
        <div style="background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 40px; text-align: center; color: #94a3b8;">
            <i class="fa-solid fa-microchip" style="font-size: 2rem; margin-bottom: 12px; color: #cbd5e1;"></i>
            <p style="margin: 0;">No hay dispositivos registrados en los hubs de esta empresa.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            @foreach($devices as $device)
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">

                    {{-- Cabecera de la Tarjeta --}}
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div>
                            <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">{{ $device->type }}</span>
                            <h3 style="margin: 4px 0 0 0; font-size: 1.05rem; color: #0f172a; line-height: 1.3;">{{ $device->name }}</h3>
                        </div>
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; background: {{ ($device->is_active ?? true) ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ ($device->is_active ?? true) ? '#10b981' : '#ef4444' }};">
                            <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> {{ ($device->is_active ?? true) ? 'ONLINE' : 'OFFLINE' }}
                        </span>
                    </div>

                    {{-- Cuerpo (Última Lectura) --}}
                    <div style="background: #f8fafc; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                        @php
                            $latestReading = $device->telemetryReadings->first();
                        @endphp

                        @if($latestReading)
                            <div style="display: flex; align-items: baseline; gap: 8px;">
                                <span style="font-size: 1.75rem; font-weight: 800; color: #1e293b;">{{ $latestReading->value }}</span>
                                <span style="font-size: 1rem; font-weight: 600; color: #64748b;">{{ $latestReading->unit }}</span>
                            </div>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">
                                <i class="fa-regular fa-clock"></i> Última lectura: {{ $latestReading->recorded_at ? $latestReading->recorded_at->diffForHumans() : $latestReading->created_at->diffForHumans() }}
                            </div>
                        @else
                            <div style="color: #94a3b8; font-size: 0.9rem; padding: 8px 0;">
                                Esperando datos de telemetría...
                            </div>
                        @endif
                    </div>

                    {{-- Acción (Botón) --}}
                    <a href="{{ route('workspace.devices.metrics', $device->id) }}" style="display: block; width: 100%; text-align: center; background: #f1f5f9; color: #334155; padding: 10px 0; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        Ver Métricas <i class="fa-solid fa-chart-line" style="margin-left: 6px;"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

@endsection
