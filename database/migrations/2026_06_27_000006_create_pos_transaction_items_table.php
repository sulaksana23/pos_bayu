<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('pos_transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('pos_products')->nullOnDelete();
            $table->string('product_name', 128); // snapshot at sale time
            $table->string('product_sku', 64)->nullable();
            $table->decimal('price', 14, 2)->default(0); // snapshot at sale time
            $table->decimal('cost', 14, 2)->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['transaction_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('pos_transaction_items'); }
};
