<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_no')->unique();
            $table->foreignId('user_id')->constrained('users')->comment('Created by');
            $table->string('category', 50)->default('operational')->comment('operational, utilities, rent, salary, maintenance, marketing, other');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('expense_date');
            $table->string('payment_method', 20)->nullable()->comment('cash, transfer');
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_expenses');
    }
};
