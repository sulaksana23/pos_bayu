<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('pos_categories')->nullOnDelete();
            $table->string('name', 128);
            $table->string('sku', 64)->unique();
            $table->string('barcode', 64)->nullable()->unique();
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('cost', 14, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->string('unit', 16)->default('pcs');
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['is_active', 'name']);
        });
    }
    public function down(): void { Schema::dropIfExists('pos_products'); }
};
