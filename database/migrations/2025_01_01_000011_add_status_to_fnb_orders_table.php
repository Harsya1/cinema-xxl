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
        Schema::table('fnb_orders', function (Blueprint $table) {
            $table->string('status')->default('paid')->after('payment_method');
            $table->string('order_code')->nullable()->after('id');
            $table->foreignId('booking_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            
            // Rename total_price to total_amount for consistency
            $table->renameColumn('total_price', 'total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fnb_orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'order_code']);
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
            $table->renameColumn('total_amount', 'total_price');
        });
    }
};
