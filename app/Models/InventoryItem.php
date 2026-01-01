<?php

namespace App\Models;

use App\Enums\InventoryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'stock_quantity',
        'unit',
        'min_stock_level',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'decimal:2',
            'type' => InventoryType::class,
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get recipes using this inventory item.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Low stock items.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    /**
     * Scope: Ingredients only.
     */
    public function scopeIngredients($query)
    {
        return $query->where('type', 'ingredient');
    }

    /**
     * Scope: Packaging only.
     */
    public function scopePackaging($query)
    {
        return $query->where('type', 'packaging');
    }

    // ==================== HELPERS ====================

    /**
     * Check if stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Add stock.
     */
    public function addStock(float $quantity): bool
    {
        return $this->increment('stock_quantity', $quantity);
    }

    /**
     * Deduct stock.
     */
    public function deductStock(float $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        return $this->decrement('stock_quantity', $quantity);
    }

    /**
     * Check if enough stock is available.
     */
    public function hasEnoughStock(float $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    /**
     * Get formatted stock with unit.
     */
    public function getFormattedStock(): string
    {
        return number_format($this->stock_quantity, 2) . ' ' . $this->unit;
    }
}
