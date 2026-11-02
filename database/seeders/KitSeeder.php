<?php

namespace Database\Seeders;

use App\Models\Kit;
use App\Models\Product;
use Illuminate\Database\Seeder;

class KitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Recuperar productos necesarios
        $hub = Product::where('sku', 'HUB-GW-01')->first();
        $tempSensor = Product::where('sku', 'SEN-TMP-HUM-01')->first();
        $doorSensor = Product::where('sku', 'SEN-MAG-DR-01')->first();
        $powerMon = Product::where('sku', 'SEN-PWR-MON-01')->first();

        if (!$hub) {
            return;
        }

        // 2. Definir Kits
        $kits = [
            [
                'sku' => 'KIT-FRIO-PRO',
                'name' => 'Kit Control Total Cadena de Frío',
                'description' => 'Solución integral para monitoreo de congeladores y cámaras frigoríficas. Incluye alertas de temperatura fuera de rango y puerta abierta.',
                'target_sector' => 'Cadena de Frío',
                'price' => 229.00,
                'items' => [
                    $hub->id => 1,
                    $tempSensor->id => 2,
                    $doorSensor->id => 1,
                ],
            ],
            [
                'sku' => 'KIT-SECURE-BIZ',
                'name' => 'Kit Comercio Seguro y Accesos',
                'description' => 'Protección y supervisión de accesos clave para pequeños negocios y almacenes.',
                'target_sector' => 'Comercio Seguro',
                'price' => 199.00,
                'items' => [
                    $hub->id => 1,
                    $doorSensor->id => 3,
                ],
            ],
            [
                'sku' => 'KIT-ENERGY-SAVE',
                'name' => 'Kit Ahorro Energético PYME',
                'description' => 'Medición de picos de consumo y gasto energético para optimización de tarifas eléctricas.',
                'target_sector' => 'Ahorro Energético',
                'price' => 189.00,
                'items' => [
                    $hub->id => 1,
                    $powerMon->id => 1,
                ],
            ],
        ];

        foreach ($kits as $kitData) {
            $items = $kitData['items'];
            unset($kitData['items']);

            $kit = Kit::updateOrCreate(
                ['sku' => $kitData['sku']],
                $kitData
            );

            // Sincronizar pivot kit_product
            $pivotData = [];
            foreach ($items as $productId => $quantity) {
                if ($productId) {
                    $pivotData[$productId] = ['quantity' => $quantity];
                }
            }
            $kit->products()->sync($pivotData);
        }
    }
}
