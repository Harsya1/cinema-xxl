<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FnbOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'fnb_order_id',
        'menu_item_id',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
        ];
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            if (empty($detail->subtotal)) {
                $menuItem = MenuItem::find($detail->menu_item_id);
                $detail->subtotal = $menuItem->price * $detail->quantity;
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the order for this detail.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(FnbOrder::class, 'fnb_order_id');
    }

    /**
     * Get the menu item.
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // ==================== HELPERS ====================

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotal(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Calculate subtotal.
     */
    public function calculateSubtotal(): float
    {
        return $this->menuItem->price * $this->quantity;
    }
}
