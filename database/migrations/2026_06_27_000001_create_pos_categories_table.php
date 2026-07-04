<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pos_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('slug', 64)->unique();
            $table->string('color', 16)->default('#f97316'); // hex
            $table->string('icon', 32)->default('fa-tag'); // font-awesome icon name (without fa-)
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pos_categories'); }
};
