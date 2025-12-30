<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Showtime;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Create a new booking.
     */
    public function createBooking(
        int $showtimeId,
        string $seatNumber,
        ?int $userId = null,
        ?int $cashierId = null,
        ?string $paymentMethod = null
    ): Booking {
        $showtime = Showtime::findOrFail($showtimeId);

        // Check if seat is available
        if (!$showtime->isSeatAvailable($seatNumber)) {
            throw new \Exception("Seat {$seatNumber} is not available.");
        }

        // Check if showtime hasn't started
        if ($showtime->start_time->isPast()) {
            throw new \Exception("This showtime has already started.");
        }

        return DB::transaction(function () use ($showtimeId, $seatNumber, $userId, $cashierId, $paymentMethod) {
            $booking = Booking::create([
                'showtime_id' => $showtimeId,
                'seat_number' => $seatNumber,
                'user_id' => $userId,
                'cashier_id' => $cashierId,
                'status' => $cashierId ? 'paid' : 'booked', // POS sales are immediately paid
                'payment_method' => $paymentMethod,
                'booking_time' => now(),
            ]);

            AuditLog::log('CREATE_BOOKING', "Booking {$booking->booking_code} created for seat {$seatNumber}");

            return $booking;
        });
    }

    /**
     * Create multiple bookings (multiple seats).
     */
    public function createMultipleBookings(
        int $showtimeId,
        array $seatNumbers,
        ?int $userId = null,
        ?int $cashierId = null,
        ?string $paymentMethod = null
    ): array {
        $bookings = [];

        DB::transaction(function () use ($showtimeId, $seatNumbers, $userId, $cashierId, $paymentMethod, &$bookings) {
            foreach ($seatNumbers as $seat) {
                $bookings[] = $this->createBooking($showtimeId, $seat, $userId, $cashierId, $paymentMethod);
            }
        });

        return $bookings;
    }

    /**
     * Process payment for a booking.
     */
    public function processPayment(Booking $booking, string $paymentMethod, ?int $cashierId = null): Booking
    {
        if ($booking->status !== 'booked') {
            throw new \Exception("Booking is not in 'booked' status.");
        }

        $booking->markAsPaid($paymentMethod, $cashierId);

        AuditLog::log('PAYMENT_PROCESSED', "Payment processed for booking {$booking->booking_code}");

        return $booking->fresh();
    }

    /**
     * Redeem a booking (scan ticket).
     */
    public function redeemBooking(string $bookingCode): Booking
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        if (!$booking->canBeRedeemed()) {
            throw new \Exception("Booking cannot be redeemed. Status: {$booking->status}");
        }

        $booking->markAsRedeemed();

        AuditLog::log('BOOKING_REDEEMED', "Booking {$bookingCode} redeemed");

        return $booking->fresh();
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(Booking $booking, string $reason = ''): Booking
    {
        if ($booking->status === 'cancelled') {
            throw new \Exception("Booking is already cancelled.");
        }

        if ($booking->status === 'redeemed') {
            throw new \Exception("Cannot cancel a redeemed booking.");
        }

        $booking->cancel();

        AuditLog::log('BOOKING_CANCELLED', "Booking {$booking->booking_code} cancelled. Reason: {$reason}");

        return $booking->fresh();
    }

    /**
     * Get booking by code.
     */
    public function findByCode(string $bookingCode): ?Booking
    {
        return Booking::with(['showtime.studio', 'user'])
            ->where('booking_code', $bookingCode)
            ->first();
    }

    /**
     * Calculate total price for seats.
     */
    public function calculateTotalPrice(int $showtimeId, int $seatCount): float
    {
        $showtime = Showtime::findOrFail($showtimeId);
        return $showtime->price * $seatCount;
    }
}
