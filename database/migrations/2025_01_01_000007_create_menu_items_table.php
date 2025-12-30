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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Popcorn Caramel
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('category', ['Food', 'Beverage', 'Combo'])->default('Food');
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
