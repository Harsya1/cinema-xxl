<?php

namespace App\Enums;

enum InventoryType: string
{
    case Ingredient = 'ingredient';
    case Packaging = 'packaging';
    case Equipment = 'equipment';

    public function label(): string
    {
        return match ($this) {
            self::Ingredient => 'Ingredient',
            self::Packaging => 'Packaging',
            self::Equipment => 'Equipment',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ingredient => 'success',
            self::Packaging => 'info',
            self::Equipment => 'warning',
        };
    }
}
