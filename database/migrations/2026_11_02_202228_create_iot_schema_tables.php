<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Hubs / Concentradores Gateway
        Schema::create('iot.hubs', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('mac_address')->unique();
            $table->foreignId('company_id')->constrained('identity.companies')->onDelete('cascade');
            $table->string('name');
            $table->string('api_token')->unique();
            $table->string('ip_address')->nullable();
            $table->string('status')->default('DISPONIBLE'); // DISPONIBLE, EN_LINEA, DESCONECTADO, MANTENIMIENTO
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamps();
        });

        // 2. Dispositivos / Sensores individuales
        Schema::create('iot.devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id')->constrained('iot.hubs')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('commerce.products')->nullOnDelete();
            $table->string('device_code'); // Código único por hub (ej: SEN-01)
            $table->string('name');
            $table->string('type'); // TEMPERATURA, HUMEDAD, ENERGIA, MOVIMIENTO, PUERTA
            $table->string('location')->nullable(); // ej: "Cámara Fría 1", "Acceso Principal"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hub_id', 'device_code']);
        });

        // 3. Mediciones de Telemetría (Serie Temporal)
        Schema::create('iot.telemetry_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('iot.devices')->onDelete('cascade');
            $table->decimal('value', 10, 2);
            $table->string('unit')->default('°C'); // °C, %, kWh, BINARY
            $table->jsonb('payload')->nullable(); // Flexibilidad para lecturas compuestas
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['device_id', 'recorded_at']);
        });

        // 4. Alertas de Operación e Eventos Criticos
        Schema::create('iot.alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('identity.companies')->onDelete('cascade');
            $table->foreignId('hub_id')->nullable()->constrained('iot.hubs')->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('iot.devices')->nullOnDelete();
            $table->string('severity')->default('ADVERTENCIA'); // INFO, ADVERTENCIA, CRITICA
            $table->string('title');
            $table->text('message');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iot.alerts');
        Schema::dropIfExists('iot.telemetry_readings');
        Schema::dropIfExists('iot.devices');
        Schema::dropIfExists('iot.hubs');
    }
};
