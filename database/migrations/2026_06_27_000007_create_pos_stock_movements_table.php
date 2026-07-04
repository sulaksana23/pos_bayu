<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('pos_products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['in', 'out', 'sale', 'return', 'adjust'])->default('in')->index();
            $table->integer('qty'); // positive for in, negative for out
            $table->decimal('unit_cost', 14, 2)->nullable();
            $table->string('reference_type', 32)->nullable(); // transaction|manual|adjust
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['product_id', 'type', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('pos_stock_movements'); }
};
