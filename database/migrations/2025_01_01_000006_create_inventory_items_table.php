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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Raw Corn, Cola Cup
            $table->enum('type', ['ingredient', 'packaging', 'equipment'])->default('ingredient');
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->string('unit'); // kg, pcs, gram
            $table->integer('min_stock_level')->default(0);
            $table->timestamps();

            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
