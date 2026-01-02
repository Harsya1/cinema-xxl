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
        Schema::table('studios', function (Blueprint $table) {
            $table->integer('rows')->default(10)->after('total_seats');
            $table->integer('cols')->default(12)->after('rows');
            $table->decimal('price_multiplier', 3, 2)->default(1.00)->after('cols');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn(['rows', 'cols', 'price_multiplier']);
        });
    }
};
