@extends('layouts.app')

@section('title', 'Centro de Alertas | Sensoría')
@section('header', 'Centro de Alertas e Incidencias IoT')

@section('content')

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 0.9rem;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h2 style="margin-top: 0; font-size: 1.1rem; color: #0f172a;">Registro de Eventos y Anomalías</h2>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
            Monitoreo en tiempo real de eventos fuera de parámetro detectados en tus sensores.
        </p>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #64748b; font-size: 0.85rem;">
                <th style="padding: 12px;">Gravedad</th>
                <th style="padding: 12px;">Alerta / Incidencia</th>
                <th style="padding: 12px;">Dispositivo</th>
                <th style="padding: 12px;">Fecha / Hora</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px; text-align: right;">Acción</th>
            </tr>
            </thead>
            <tbody>
            @forelse($alerts as $alert)
                <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                    <td style="padding: 12px;">
                        @php
                            $bg = '#f1f5f9'; $color = '#475569';
                            if ($alert->severity === 'ALTA' || $alert->severity === 'CRITICA') {
                                $bg = 'rgba(239, 68, 68, 0.1)'; $color = '#ef4444';
                            } elseif ($alert->severity === 'MEDIA') {
                                $bg = 'rgba(245, 158, 11, 0.1)'; $color = '#f59e0b';
                            }
                        @endphp
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; background: {{ $bg }}; color: {{ $color }};">
                            {{ $alert->severity }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <strong style="color: #0f172a; display: block;">{{ $alert->title }}</strong>
                        <span style="font-size: 0.85rem; color: #64748b;">{{ $alert->message }}</span>
                    </td>
                    <td style="padding: 12px; color: #475569; font-weight: 500;">
                        {{ $alert->device->name ?? 'Dispositivo General' }}
                    </td>
                    <td style="padding: 12px; color: #64748b; font-size: 0.85rem;">
                        {{ $alert->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td style="padding: 12px;">
                        @if($alert->is_resolved)
                            <span style="color: #10b981; font-weight: 600; font-size: 0.85rem;">
                                <i class="fa-solid fa-check-double"></i> RESUELTA
                            </span>
                        @else
                            <span style="color: #ef4444; font-weight: 600; font-size: 0.85rem;">
                                <i class="fa-solid fa-triangle-exclamation"></i> ACTIVA
                            </span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        @if(!$alert->is_resolved)
                            <form action="{{ route('workspace.alerts.resolve', $alert->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                    Resolver
                                </button>
                            </form>
                        @else
                            <span style="font-size: 0.8rem; color: #94a3b8;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 32px; color: #94a3b8;">
                        No hay alertas o incidencias registradas para esta empresa.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $alerts->links() }}
        </div>
    </div>
@endsection
