@extends('layouts.app')

@section('title', 'Historial de Actividad | Sensoría IoT')
@section('header', 'Bitácora de Auditoría')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding-bottom: 40px;">

        <!-- Contenedor principal con Glassmorphism -->
        <div class="glass-surface" style="padding: 32px; border-radius: 12px; margin-bottom: 24px;">

            <!-- Cabecera de la sección -->
            <div style="margin-bottom: 24px; border-bottom: 1px solid rgba(27, 61, 26, 0.1); padding-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-tertiary); margin: 0;">
                        Registro de Eventos en {{ $company->trade_name }}
                    </h3>
                    <p style="color: #718096; font-size: 0.9rem; margin-top: 4px;">
                        Historial de acciones críticas y cambios realizados en este espacio.
                    </p>
                </div>

                <!-- Buscador Nativo -->
                <div>
                    <input type="text" id="activity-search" onkeyup="filterActivities()" placeholder="Filtrar eventos..." style="padding: 8px 14px; border: 1px solid rgba(27, 61, 26, 0.2); border-radius: var(--radius-button); font-size: 0.875rem; outline: none; background: #ffffff;">
                </div>
            </div>

            <!-- Tabla de Auditoría -->
            <div style="overflow-x: auto;">
                <table id="activity-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                    <tr style="border-bottom: 1px solid rgba(27, 61, 26, 0.1); color: var(--color-tertiary);">
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Fecha y Hora</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Usuario</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Categoría</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 0.875rem;">Acción / Detalle</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($activities as $log)
                        <tr class="activity-row" style="border-bottom: 1px solid rgba(27, 61, 26, 0.05);">
                            <td style="padding: 16px; color: #718096; font-size: 0.875rem; white-space: nowrap;">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td style="padding: 16px; color: var(--color-tertiary); font-weight: 600; font-size: 0.9rem;">
                                {{ $log->user->name ?? 'Sistema' }}
                            </td>

                            <td style="padding: 16px;">
                                @php
                                    $badgeColor = match($log->category) {
                                        'EQUIPO' => 'rgba(88, 148, 69, 0.15)',
                                        'AJUSTES' => 'rgba(221, 107, 32, 0.15)',
                                        'SEGURIDAD' => 'rgba(229, 62, 62, 0.15)',
                                        default => 'rgba(113, 128, 150, 0.15)',
                                    };
                                    $textColor = match($log->category) {
                                        'EQUIPO' => 'var(--color-primary)',
                                        'AJUSTES' => '#dd6b20',
                                        'SEGURIDAD' => '#e53e3e',
                                        default => '#4a5568',
                                    };
                                @endphp
                                <span style="background-color: {{ $badgeColor }}; color: {{ $textColor }}; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                {{ $log->category }}
                            </span>
                            </td>

                            <td style="padding: 16px; color: var(--color-tertiary); font-size: 0.9rem;">
                                <strong style="display: block;">{{ $log->action }}</strong>
                                <span style="color: #718096; font-size: 0.825rem;">{{ $log->description }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 32px; text-align: center; color: #718096;">
                                No hay registros de actividad recientes en este espacio.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function filterActivities() {
            const input = document.getElementById('activity-search').value.toLowerCase();
            const rows = document.querySelectorAll('.activity-row');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        }
    </script>
@endpush
