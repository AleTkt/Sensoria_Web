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
        Schema::table('commerce.products', function (Blueprint $table) {
            // Añadimos la columna para guardar el indicador/ruta de la imagen en Supabase
            $table->string('image_path')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commerce.products', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
