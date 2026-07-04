<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('opening_cash', 14, 2)->default(0);
            $table->decimal('closing_cash', 14, 2)->nullable();
            $table->decimal('expected_cash', 14, 2)->nullable();
            $table->decimal('cash_difference', 14, 2)->nullable(); // positive = surplus, negative = shortage
            $table->unsignedInteger('transaction_count')->default(0);
            $table->decimal('total_sales', 14, 2)->default(0);
            $table->decimal('total_cash', 14, 2)->default(0);
            $table->decimal('total_non_cash', 14, 2)->default(0);
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open')->index();
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('pos_shifts'); }
};
