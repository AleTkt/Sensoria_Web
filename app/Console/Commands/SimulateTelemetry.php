<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hub;
use App\Models\Device;
use App\Models\TelemetryReading;
use Illuminate\Support\Str;
use App\Models\Alert;

class SimulateTelemetry extends Command
{
    /**
     * El nombre y firma del comando para la consola (php artisan iot:simulate).
     *
     * @var string
     */
    protected $signature = 'iot:simulate';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Genera lecturas de telemetría simuladas para todos los dispositivos conectados a Hubs activos.';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle()
    {
        // 1. Obtener todos los Hubs que estén en estado ONLINE
        $hubs = Hub::where('status', 'ONLINE')->get();

        if ($hubs->isEmpty()) {
            $this->info('No hay Hubs activos (ONLINE) para simular telemetría.');
            return Command::SUCCESS;
        }

        $readingsCount = 0;

        foreach ($hubs as $hub) {
            // Actualizamos la marca de tiempo del ping del Hub
            $hub->last_ping_at = now();
            $hub->save();

            // 2. Obtener los dispositivos activos vinculados a este Hub
            $devices = Device::where('hub_id', $hub->id)
                ->where('is_active', true)
                ->get();

            foreach ($devices as $device) {
                // 3. Generar un valor y unidad coherente según el tipo de sensor
                $sensorData = $this->generateSensorReading($device->type, $device->name);

                // 4. Registrar la lectura en la base de datos
                $reading = TelemetryReading::create([
                    'device_id' => $device->id,
                    'value' => $sensorData['value'],
                    'unit' => $sensorData['unit'],
                    'payload' => [
                        'simulated' => true,
                        'battery_level' => rand(80, 100) . '%',
                        'signal_strength' => rand(-70, -40) . ' dBm'
                    ],
                    'recorded_at' => now(),
                ]);

                // 5. Evaluamos si el valor recién generado requiere disparar una alerta
                $this->evaluateAlerts($hub, $device, $sensorData);

                $readingsCount++;
            }
        }

        $this->info("¡Éxito! Se generaron {$readingsCount} lecturas de telemetría para " . $hubs->count() . " Hub(s) activo(s).");
        return Command::SUCCESS;
    }

    /**
     * Genera un valor realista y su unidad según el tipo de dispositivo.
     */
    private function generateSensorReading(string $type, string $name): array
    {
        $textToAnalyze = strtoupper($type . ' ' . $name);

        if (str_contains($textToAnalyze, 'TEMP') || str_contains($textToAnalyze, 'TERMOSTATO')) {
            return [
                'value' => round(rand(250, 680) / 100, 2),
                'unit' => '°C'
            ];
        }

        if (str_contains($textToAnalyze, 'HUMEDAD')) {
            return [
                'value' => round(rand(4000, 6500) / 100, 2),
                'unit' => '%'
            ];
        }

        if (str_contains($textToAnalyze, 'CONSUMO') || str_contains($textToAnalyze, 'MEDIDOR') || str_contains($textToAnalyze, 'ENERGIA')) {
            return [
                'value' => round(rand(120, 350) / 100, 2),
                'unit' => 'kWh'
            ];
        }

        if (str_contains($textToAnalyze, 'PUERTA') || str_contains($textToAnalyze, 'CONTACTO') || str_contains($textToAnalyze, 'MAGNETICO')) {
            return [
                'value' => rand(0, 10) > 8 ? 1.00 : 0.00,
                'unit' => 'ESTADO'
            ];
        }

        return [
            'value' => round(rand(100, 999) / 100, 2),
            'unit' => 'N/A'
        ];
    }

    /**
     * Evalúa los límites del sensor e inserta una alerta si hay una anomalía.
     */
    private function evaluateAlerts(Hub $hub, Device $device, array $sensorData): void
    {
        $value = $sensorData['value'];
        $unit = $sensorData['unit'];
        $textToAnalyze = strtoupper($device->type . ' ' . $device->name);

        $title = null;
        $message = null;
        $severity = 'MEDIA';

        // 1. Regla para Temperatura: Alerta si pasa de 6.0 °C o baja de 1.0 °C
        if (str_contains($textToAnalyze, 'TEMP') || str_contains($textToAnalyze, 'TERMOSTATO')) {
            if ($value > 6.0) {
                $title = 'Temperatura Elevada Detectada';
                $message = "El dispositivo {$device->name} registró {$value} {$unit}, superando el umbral máximo de 6.0 °C.";
                $severity = 'ALTA';
            } elseif ($value < 1.0) {
                $title = 'Temperatura Inusualmente Baja';
                $message = "El dispositivo {$device->name} registró {$value} {$unit}, por debajo del umbral mínimo de 1.0 °C.";
                $severity = 'MEDIA';
            }
        }

        // 2. Regla para Consumo Eléctrico: Alerta si sobrepasa 3.0 kWh
        if (str_contains($textToAnalyze, 'CONSUMO') || str_contains($textToAnalyze, 'MEDIDOR') || str_contains($textToAnalyze, 'ENERGIA')) {
            if ($value > 3.0) {
                $title = 'Pico de Consumo Eléctrico';
                $message = "Se ha detectado un consumo elevado de {$value} {$unit} en {$device->name}.";
                $severity = 'ALTA';
            }
        }

        // 3. Regla para Sensores de Puerta: Alerta si el estado es 1 (Abierta)
        if (str_contains($textToAnalyze, 'PUERTA') || str_contains($textToAnalyze, 'CONTACTO') || str_contains($textToAnalyze, 'MAGNETICO')) {
            if ($value == 1.00) {
                $title = 'Puerta / Acceso Abierto';
                $message = "El sensor {$device->name} reporta acceso abierto en las instalaciones.";
                $severity = 'MEDIA';
            }
        }

        // Si se cumplió alguna condición de anomalía, registramos la alerta
        if ($title && $message) {
            Alert::create([
                'company_id' => $hub->company_id,
                'hub_id' => $hub->id,
                'device_id' => $device->id,
                'title' => $title,
                'message' => $message,
                'severity' => $severity,
                'is_resolved' => false,
            ]);
        }
    }
}
