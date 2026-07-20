<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('pos_purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('pos_products');
            $table->string('product_name');
            $table->string('product_sku', 50)->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('price', 15, 2)->default(0)->comment('Unit price');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_purchase_order_items');
    }
};
