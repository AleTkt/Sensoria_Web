<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identity.company_activity_logs', function (Blueprint $table) {
            $table->id();

            // Especificamos el esquema 'identity.' para ambas llaves foráneas
            $table->foreignId('company_id')
                ->constrained('identity.companies')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('identity.users')
                ->nullOnDelete();

            $table->string('category');
            $table->string('action');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('identity.company_activity_logs');
    }
};
