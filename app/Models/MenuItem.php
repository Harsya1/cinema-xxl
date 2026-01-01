<?php

namespace App\Models;

use App\Enums\MenuCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image_path',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'category' => MenuCategory::class,
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get recipes for this menu item.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Get order details for this menu item.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(FnbOrderDetail::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Available menu items only.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: Food items only.
     */
    public function scopeFood($query)
    {
        return $query->where('category', 'Food');
    }

    /**
     * Scope: Beverages only.
     */
    public function scopeBeverages($query)
    {
        return $query->where('category', 'Beverage');
    }

    /**
     * Scope: Combos only.
     */
    public function scopeCombos($query)
    {
        return $query->where('category', 'Combo');
    }

    // ==================== HELPERS ====================

    /**
     * Check if this menu item can be made based on inventory.
     */
    public function canBeMade(int $quantity = 1): bool
    {
        foreach ($this->recipes as $recipe) {
            $needed = $recipe->quantity_needed * $quantity;
            if (!$recipe->inventoryItem->hasEnoughStock($needed)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Deduct inventory when item is sold.
     */
    public function deductInventory(int $quantity = 1): bool
    {
        foreach ($this->recipes as $recipe) {
            $needed = $recipe->quantity_needed * $quantity;
            if (!$recipe->inventoryItem->deductStock($needed)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get missing ingredients for this menu item.
     */
    public function getMissingIngredients(int $quantity = 1): array
    {
        $missing = [];

        foreach ($this->recipes as $recipe) {
            $needed = $recipe->quantity_needed * $quantity;
            if (!$recipe->inventoryItem->hasEnoughStock($needed)) {
                $missing[] = [
                    'ingredient' => $recipe->inventoryItem->name,
                    'needed' => $needed,
                    'available' => $recipe->inventoryItem->stock_quantity,
                    'unit' => $recipe->inventoryItem->unit,
                ];
            }
        }

        return $missing;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPrice(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get image URL.
     */
    public function getImageUrl(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return asset('storage/' . $this->image_path);
    }
}
