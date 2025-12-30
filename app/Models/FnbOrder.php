<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'cashier_id',
        'user_id',
        'booking_id',
        'total_amount',
        'payment_method',
        'status',
        'transaction_time',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'transaction_time' => 'datetime',
            'status' => \App\Enums\BookingStatus::class,
            'payment_method' => \App\Enums\PaymentMethod::class,
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the cashier who processed this order.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the customer who made this order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the linked booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get order details.
     */
    public function details(): HasMany
    {
        return $this->hasMany(FnbOrderDetail::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Orders for today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('transaction_time', today());
    }

    /**
     * Scope: Orders by cashier.
     */
    public function scopeByCashier($query, int $cashierId)
    {
        return $query->where('cashier_id', $cashierId);
    }

    // ==================== HELPERS ====================

    /**
     * Calculate total from order details.
     */
    public function calculateTotal(): float
    {
        return $this->details->sum('subtotal');
    }

    /**
     * Update total price based on order details.
     */
    public function updateTotalAmount(): bool
    {
        return $this->update(['total_amount' => $this->calculateTotal()]);
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotal(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Check if this is a walk-in order.
     */
    public function isWalkIn(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Add item to order.
     */
    public function addItem(int $menuItemId, int $quantity): FnbOrderDetail
    {
        $menuItem = MenuItem::findOrFail($menuItemId);

        $detail = $this->details()->create([
            'menu_item_id' => $menuItemId,
            'quantity' => $quantity,
            'subtotal' => $menuItem->price * $quantity,
        ]);

        // Deduct inventory
        $menuItem->deductInventory($quantity);

        // Update total
        $this->updateTotalPrice();

        return $detail;
    }
}
