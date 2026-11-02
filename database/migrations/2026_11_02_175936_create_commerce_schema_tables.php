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
        // 1. Productos de Hardware/Sensores
        Schema::create('commerce.products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('category')->default('SENSOR'); // HUB, SENSOR, ACTUADOR
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Kits Autoinstalables (Comercio Seguro, Cadena de Frío, etc.)
        Schema::create('commerce.kits', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('target_sector'); // Comercio, Cadena de Frío, Ahorro Energético
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Pivote Kit - Producto (Qué productos y cuántos forman cada Kit)
        Schema::create('commerce.kit_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_id')->constrained('commerce.kits')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('commerce.products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['kit_id', 'product_id']);
        });

        // 4. Órdenes / Pedidos de Compra (Asociados a la PYME en identity.companies)
        Schema::create('commerce.orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('company_id')->constrained('identity.companies')->onDelete('restrict');
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('PENDIENTE'); // PENDIENTE, PAGADO, DESPACHADO, CANCELADO
            $table->string('payment_method')->nullable(); // SIMULADO_TARJETA, SIMULADO_TRANSFERENCIA
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 5. Detalle de Ítems Comprados en la Orden
        Schema::create('commerce.order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('commerce.orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('commerce.products')->nullOnDelete();
            $table->foreignId('kit_id')->nullable()->constrained('commerce.kits')->nullOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commerce.order_items');
        Schema::dropIfExists('commerce.orders');
        Schema::dropIfExists('commerce.kit_product');
        Schema::dropIfExists('commerce.kits');
        Schema::dropIfExists('commerce.products');
    }
};
