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
        Schema::create('identity.payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('identity.users')->onDelete('cascade');
            $table->string('cardholder_name');
            $table->string('brand'); // Ej: Visa, MasterCard, Amex
            $table->string('last_4_digits', 4);
            $table->string('exp_month', 2);
            $table->string('exp_year', 4);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity.payment_methods');
    }
};
