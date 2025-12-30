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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // Unique code like CXXL-8821
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Nullable for walk-in guest
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete(); // Filled if booked via POS
            $table->foreignId('showtime_id')->constrained()->cascadeOnDelete();
            $table->string('seat_number'); // e.g., A1
            $table->enum('status', ['booked', 'paid', 'redeemed', 'cancelled'])->default('booked');
            $table->string('payment_method')->nullable();
            $table->timestamp('booking_time')->useCurrent();
            $table->timestamps();

            $table->unique(['showtime_id', 'seat_number']); // Prevent double booking
            $table->index('booking_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
