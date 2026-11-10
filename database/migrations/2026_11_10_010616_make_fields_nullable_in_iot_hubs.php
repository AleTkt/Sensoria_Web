<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iot.hubs', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->change();
            $table->string('mac_address')->nullable()->change();
            $table->string('api_token')->nullable()->change();

            // Opcional: Cambiamos el estado por defecto para que nazca desconectado
            $table->string('status')->default('DESCONECTADO')->change();
        });
    }

    public function down(): void
    {
        Schema::table('iot.hubs', function (Blueprint $table) {
            $table->string('serial_number')->nullable(false)->change();
            $table->string('mac_address')->nullable(false)->change();
            $table->string('api_token')->nullable(false)->change();
            $table->string('status')->default('DISPONIBLE')->change();
        });
    }
};
