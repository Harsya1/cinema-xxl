<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'cashier_id',
        'showtime_id',
        'seat_number',
        'status',
        'payment_method',
        'booking_time',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'booking_time' => 'datetime',
        ];
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = self::generateBookingCode();
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the customer who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cashier who processed the booking.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the showtime for this booking.
     */
    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Active bookings (not cancelled).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['booked', 'paid', 'redeemed']);
    }

    /**
     * Scope: Pending payment bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'booked');
    }

    /**
     * Scope: Paid bookings.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ==================== HELPERS ====================

    /**
     * Generate a unique booking code.
     */
    public static function generateBookingCode(): string
    {
        do {
            $code = 'CXXL-' . strtoupper(Str::random(4)) . rand(1000, 9999);
        } while (self::where('booking_code', $code)->exists());

        return $code;
    }

    /**
     * Mark booking as paid.
     */
    public function markAsPaid(string $paymentMethod, ?int $cashierId = null): bool
    {
        return $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'cashier_id' => $cashierId ?? $this->cashier_id,
        ]);
    }

    /**
     * Mark booking as redeemed (ticket used).
     */
    public function markAsRedeemed(): bool
    {
        return $this->update(['status' => 'redeemed']);
    }

    /**
     * Cancel the booking.
     */
    public function cancel(): bool
    {
        return $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if booking can be redeemed.
     */
    public function canBeRedeemed(): bool
    {
        return $this->status === 'paid' && !$this->showtime->hasEnded();
    }

    /**
     * Check if booking is a walk-in (no user).
     */
    public function isWalkIn(): bool
    {
        return is_null($this->user_id);
    }
}
