<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 32)->unique();
            $table->foreignId('shift_id')->constrained('pos_shifts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // cashier
            $table->foreignId('customer_id')->nullable()->constrained('pos_customers')->nullOnDelete();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('paid', 14, 2)->default(0);
            $table->decimal('change_amount', 14, 2)->default(0);
            $table->enum('payment_method', ['cash', 'qris', 'transfer', 'wallet', 'mixed'])->default('cash')->index();
            $table->json('payment_details')->nullable(); // for split payments
            $table->enum('status', ['completed', 'void', 'pending', 'held'])->default('completed')->index();
            $table->text('notes')->nullable();
            $table->string('void_reason')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['created_at', 'status']);
            $table->index(['shift_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('pos_transactions'); }
};
