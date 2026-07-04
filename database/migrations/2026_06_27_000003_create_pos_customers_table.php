<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('phone', 32)->nullable()->index();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->integer('points')->default(0);
            $table->decimal('total_spent', 14, 2)->default(0);
            $table->integer('visit_count')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pos_customers'); }
};
