@extends('layouts.app')

@section('title', 'Métricas del Sensor | Sensoría')
@section('header', 'Telemetría: ' . $device->name)

@section('content')
    {{-- Cabecera del Dispositivo --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">TIPO: {{ $device->type }}</span>
                <h2 style="margin: 4px 0 0 0; font-size: 1.5rem; color: #0f172a;">{{ $device->name }}</h2>
                <p style="margin: 4px 0 0 0; font-size: 0.9rem; color: #64748b;">Código de Hardware: {{ $device->device_code ?? $device->id }}</p>
            </div>
            <a href="{{ route('workspace.devices') }}" style="padding: 10px 20px; background: #f1f5f9; color: #475569; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: background 0.2s;">
                <i class="fa-solid fa-arrow-left"></i> Volver a Dispositivos
            </a>
        </div>
    </div>

    {{-- Contenedor Principal dividido en 2 columnas --}}
    <div style="display: flex; flex-wrap: wrap; gap: 24px;">

        {{-- Columna Izquierda: Gráfico de Chart.js --}}
        <div style="flex: 2; min-width: 500px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #0f172a;">Flujo de Telemetría (Últimas 30 lecturas)</h3>
                <span style="background: rgba(88, 148, 69, 0.1); color: #589445; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700;">
                    <i class="fa-solid fa-circle-dot" style="font-size: 0.6rem; margin-right: 4px;"></i> En Vivo
                </span>
            </div>

            {{-- El lienzo (Canvas) donde se dibujará la gráfica --}}
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="metricsChart"></canvas>
            </div>
        </div>

        {{-- Columna Derecha: Tabla de Historial Compacta --}}
        <div style="flex: 1; min-width: 300px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); max-height: 445px; overflow-y: auto;">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; color: #0f172a; position: sticky; top: -24px; background: #fff; padding-top: 24px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0;">Registro Detallado</h3>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                <tr style="text-align: left; color: #64748b; font-size: 0.8rem; text-transform: uppercase;">
                    <th style="padding: 10px 8px;">Valor</th>
                    <th style="padding: 10px 8px;">Hora / Fecha</th>
                </tr>
                </thead>
                <tbody>
                @forelse($readings as $reading)
                    <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                        <td style="padding: 12px 8px; font-weight: 700; color: #0f172a;">
                            {{ $reading->value }} <span style="color: #64748b; font-weight: 500; font-size: 0.8rem;">{{ $reading->unit ?? '' }}</span>
                        </td>
                        <td style="padding: 12px 8px; color: #64748b; font-size: 0.85rem;">
                            <strong style="color: #475569;">{{ $reading->created_at->format('H:i') }} hrs</strong>
                            <div style="font-size: 0.75rem; color: #94a3b8;">{{ $reading->created_at->format('d/m/Y') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 24px; color: #94a3b8;">Sin datos registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Importamos Chart.js desde CDN y configuramos el gráfico --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtenemos las lecturas desde Laravel y las invertimos (.reverse)
            // para que en el gráfico el dato más antiguo salga a la izquierda y el actual a la derecha.
            const rawData = @json($readings->reverse()->values());

            if (rawData.length === 0) return;

            // Preparamos las etiquetas del eje X (Las horas)
            const labels = rawData.map(r => {
                let date = new Date(r.created_at);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            });

            // Preparamos los valores del eje Y
            const dataValues = rawData.map(r => r.value);
            const unit = rawData[0].unit || '';

            const ctx = document.getElementById('metricsChart').getContext('2d');

            // Creamos un degradado verde (del color de Sensoría) para rellenar debajo de la línea
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(88, 148, 69, 0.4)');
            gradient.addColorStop(1, 'rgba(88, 148, 69, 0.0)');

            // Inicializamos el Gráfico
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Lectura',
                        data: dataValues,
                        borderColor: '#589445',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#589445',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Hace que la línea no sea recta, sino curva y suave
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, // Ocultamos la leyenda superior
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 14, weight: 'bold' },
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' ' + unit;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false, // Permite que el gráfico se auto-ajuste si son temperaturas altas
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { color: '#64748b' }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: '#64748b', maxTicksLimit: 8 }
                        }
                    }
                }
            });
        });
    </script>
@endsection
