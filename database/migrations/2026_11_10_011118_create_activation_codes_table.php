<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce.activation_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('commerce.orders')->onDelete('cascade');

            $table->string('code')->unique();

            $table->string('status')->default('PENDIENTE');

            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce.activation_codes');
    }
};
