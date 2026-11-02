<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'sku' => 'HUB-GW-01',
                'name' => 'Gateway Central Sensoria Hub Pro',
                'description' => 'Concentrador de telemetría IoT multi-protocolo con conectividad WiFi/Ethernet y respaldo de batería.',
                'price' => 149.99,
                'stock' => 50,
                'category' => 'HUB',
                'is_active' => true,
            ],
            [
                'sku' => 'SEN-TMP-HUM-01',
                'name' => 'Sensor de Temperatura y Humedad Ambient / Cámara Fría',
                'description' => 'Sensor de alta precisión (-40°C a +85°C) con grado de protección IP67 para cámaras de refrigeración.',
                'price' => 39.50,
                'stock' => 150,
                'category' => 'SENSOR',
                'is_active' => true,
            ],
            [
                'sku' => 'SEN-MAG-DR-01',
                'name' => 'Sensor Magnético de Apertura de Puerta',
                'description' => 'Sensor de estado de puerta/acceso para detección de intromisión o puertas de cámaras frigoríficas abiertas.',
                'price' => 24.90,
                'stock' => 200,
                'category' => 'SENSOR',
                'is_active' => true,
            ],
            [
                'sku' => 'SEN-PWR-MON-01',
                'name' => 'Módulo Medidor de Consumo Eléctrico monofásico',
                'description' => 'Pinza transformadora de corriente para monitoreo continuo de kWh y consumo energético en tiempo real.',
                'price' => 59.99,
                'stock' => 80,
                'category' => 'SENSOR',
                'is_active' => true,
            ],
            [
                'sku' => 'ACT-RLY-SW-01',
                'name' => 'Actuador de Relé Inteligente (Switch On/Off)',
                'description' => 'Módulo actuador para encendido/apagado remoto automatizado ante alertas críticas.',
                'price' => 45.00,
                'stock' => 60,
                'category' => 'ACTUADOR',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }
    }
}
