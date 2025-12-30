<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'inventory_item_id',
        'quantity_needed',
    ];

    protected function casts(): array
    {
        return [
            'quantity_needed' => 'decimal:2',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the menu item for this recipe.
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the inventory item used in this recipe.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    // ==================== HELPERS ====================

    /**
     * Get formatted quantity with unit.
     */
    public function getFormattedQuantity(): string
    {
        return number_format($this->quantity_needed, 2) . ' ' . $this->inventoryItem->unit;
    }

    /**
     * Check if enough inventory is available for this recipe.
     */
    public function hasEnoughInventory(int $multiplier = 1): bool
    {
        return $this->inventoryItem->hasEnoughStock($this->quantity_needed * $multiplier);
    }
}
