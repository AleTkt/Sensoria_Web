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
        // Tabla de Empresas (PYMEs)
        Schema::create('identity.companies', function (Blueprint $table) {
            $table->id();
            $table->string('tax_id')->unique()->nullable(); // RFC / NIT / RUT según país
            $table->string('legal_name'); // Razón Social
            $table->string('trade_name')->nullable(); // Nombre Comercial
            $table->string('sector')->nullable(); // Comercio, Cadena de Frío, Servicios, etc.
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla Pivote: Membresía / Usuarios por Empresa
        Schema::create('identity.company_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('identity.users')
                ->onDelete('cascade');

            $table->foreignId('company_id')
                ->constrained('identity.companies')
                ->onDelete('cascade');

            $table->string('company_role')->default('ENCARGADO'); // PROPIETARIO, ENCARGADO, TECNICO
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Garantizar que no existan duplicados de membresía
            $table->unique(['user_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity.company_user');
        Schema::dropIfExists('identity.companies');
    }
};
