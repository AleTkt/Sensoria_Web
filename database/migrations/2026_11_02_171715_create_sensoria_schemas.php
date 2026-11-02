<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS identity;');
        DB::statement('CREATE SCHEMA IF NOT EXISTS commerce;');
        DB::statement('CREATE SCHEMA IF NOT EXISTS crm;');
        DB::statement('CREATE SCHEMA IF NOT EXISTS iot;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // NOTA: No eliminamos los schemas con DROP SCHEMA en la marcha atras
        // para evitar perdidas accidentales de tablas o cascadas de datos durante pruebas.
    }
};
