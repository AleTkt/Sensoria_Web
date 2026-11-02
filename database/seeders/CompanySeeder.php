<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Company;
use App\Models\Device;
use App\Models\Hub;
use App\Models\Kit;
use App\Models\Order;
use App\Models\Product;
use App\Models\TelemetryReading;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener productos y kits base
        $kitFrio = Kit::where('sku', 'KIT-FRIO-PRO')->first();
        $kitSecure = Kit::where('sku', 'KIT-SECURE-BIZ')->first();
        $tempSensorProd = Product::where('sku', 'SEN-TMP-HUM-01')->first();
        $doorSensorProd = Product::where('sku', 'SEN-MAG-DR-01')->first();

        // --- EMPRESA 1: Cadena de Frío ---
        $companyFrio = Company::updateOrCreate(
            ['tax_id' => '98765432-1'],
            [
                'legal_name' => 'Carnicería & Embutidos La Regia S.A.',
                'trade_name' => 'La Regia Gourmet',
                'sector' => 'Cadena de Frío',
                'email' => 'contacto@laregiagourmet.com',
                'phone' => '+52 55 1234 5678',
                'address' => 'Av. Industrial #402, Bodega 5, Ciudad de México',
                'is_active' => true,
            ]
        );

        // Usuario Encargado de La Regia
        $userFrio = User::updateOrCreate(
            ['email' => 'gerente@laregiagourmet.com'],
            [
                'name' => 'Carlos Mendoza',
                'password' => bcrypt('password123'),
                'global_role' => 'CLIENTE',
            ]
        );
        $userFrio->companies()->syncWithoutDetaching([
            $companyFrio->id => ['company_role' => 'PROPIETARIO', 'is_active' => true]
        ]);

        // Orden de Compra para La Regia
        if ($kitFrio) {
            $orderFrio = Order::updateOrCreate(
                ['order_number' => 'ORD-2026-0002'],
                [
                    'company_id' => $companyFrio->id,
                    'total_amount' => $kitFrio->price,
                    'status' => 'PAGADO',
                    'payment_method' => 'SIMULADO_TARJETA',
                    'paid_at' => now()->subDays(5),
                ]
            );
            $orderFrio->items()->updateOrCreate(
                ['order_id' => $orderFrio->id, 'kit_id' => $kitFrio->id],
                [
                    'quantity' => 1,
                    'unit_price' => $kitFrio->price,
                    'subtotal' => $kitFrio->price,
                ]
            );
        }

        // Infrastructure IoT de La Regia
        $hubFrio = Hub::updateOrCreate(
            ['serial_number' => 'HUB-REGIA-01'],
            [
                'mac_address' => 'AA:BB:CC:DD:EE:01',
                'company_id' => $companyFrio->id,
                'name' => 'Gateway Central Cámara Fría',
                'api_token' => Str::random(40),
                'ip_address' => '192.168.1.150',
                'status' => 'EN_LINEA',
                'last_ping_at' => now(),
            ]
        );

        $deviceFrio = Device::updateOrCreate(
            ['hub_id' => $hubFrio->id, 'device_code' => 'SEN-CAM-01'],
            [
                'product_id' => $tempSensorProd?->id,
                'name' => 'Sensor Congelador Principal',
                'type' => 'TEMPERATURA',
                'location' => 'Cámara Fría #1',
                'is_active' => true,
            ]
        );

        // Simular lecturas históricas de telemetría (últimas 6 horas)
        for ($i = 6; $i >= 0; $i--) {
            // Simular variación entre -19.5 °C y -17.0 °C
            $tempVal = -18.5 + (rand(-10, 10) / 10);
            TelemetryReading::create([
                'device_id' => $deviceFrio->id,
                'value' => $tempVal,
                'unit' => '°C',
                'payload' => ['humidity' => rand(70, 85), 'rssi' => -62],
                'recorded_at' => now()->subHours($i),
            ]);
        }

        // Generar 1 Alerta para La Regia
        Alert::updateOrCreate(
            ['company_id' => $companyFrio->id, 'title' => 'Puerta de congelador abierta'],
            [
                'hub_id' => $hubFrio->id,
                'device_id' => $deviceFrio->id,
                'severity' => 'ADVERTENCIA',
                'message' => 'La temperatura ascendió a -12.0°C durante más de 5 minutos.',
                'is_resolved' => true,
                'resolved_at' => now()->subHours(2),
            ]
        );

        // --- EMPRESA 2: Comercio Seguro ---
        $companySecure = Company::updateOrCreate(
            ['tax_id' => '11223344-5'],
            [
                'legal_name' => 'Farmacia Santa Cruz S.A. de C.V.',
                'trade_name' => 'Farmacias Santa Cruz',
                'sector' => 'Comercio Seguro',
                'email' => 'administracion@farmaciasantacruz.com',
                'phone' => '+52 55 8765 4321',
                'address' => 'Calle Principal #105, Col. Centro',
                'is_active' => true,
            ]
        );

        $userSecure = User::updateOrCreate(
            ['email' => 'operaciones@farmaciasantacruz.com'],
            [
                'name' => 'Lucía Gómez',
                'password' => bcrypt('password123'),
                'global_role' => 'CLIENTE',
            ]
        );
        $userSecure->companies()->syncWithoutDetaching([
            $companySecure->id => ['company_role' => 'ENCARGADO', 'is_active' => true]
        ]);

        // Ticket CRM para Farmacia Santa Cruz
        $ticket = Ticket::updateOrCreate(
            ['ticket_number' => 'TCK-2026-0002'],
            [
                'company_id' => $companySecure->id,
                'user_id' => $userSecure->id,
                'subject' => 'Consulta sobre ampliación de rango de sensores magnéticos',
                'category' => 'GENERAL',
                'priority' => 'MEDIA',
                'status' => 'EN_PROCESO',
            ]
        );
        $ticket->messages()->updateOrCreate(
            ['ticket_id' => $ticket->id, 'user_id' => $userSecure->id],
            [
                'message' => 'Deseamos instalar dos sensores adicionales en las cortinas metálicas del local.',
                'is_internal_note' => false,
            ]
        );
    }
}
