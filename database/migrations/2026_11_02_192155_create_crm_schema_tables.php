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
        // 1. Tickets de Soporte
        Schema::create('crm.tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('company_id')->constrained('identity.companies')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('identity.users')->onDelete('cascade');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('identity.users')->nullOnDelete();

            $table->string('subject');
            $table->string('category')->default('TECNICO'); // TECNICO, FACTURACION, GENERAL, DISPOSITIVO
            $table->string('priority')->default('MEDIA'); // BAJA, MEDIA, ALTA, CRITICA
            $table->string('status')->default('ABIERTO'); // ABIERTO, EN_PROCESO, RESUELTO, CERRADO
            $table->timestamps();
        });

        // 2. Mensajes / Respuestas del Ticket
        Schema::create('crm.ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('crm.tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('identity.users')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_internal_note')->default(false); // Para notas privadas entre el personal de soporte
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm.ticket_messages');
        Schema::dropIfExists('crm.tickets');
    }
};
