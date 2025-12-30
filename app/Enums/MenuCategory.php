<?php

namespace App\Enums;

enum MenuCategory: string
{
    case Food = 'Food';
    case Beverage = 'Beverage';
    case Combo = 'Combo';

    public function label(): string
    {
        return match ($this) {
            self::Food => 'Food',
            self::Beverage => 'Beverage',
            self::Combo => 'Combo',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Food => 'warning',
            self::Beverage => 'info',
            self::Combo => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Food => 'heroicon-o-cake',
            self::Beverage => 'heroicon-o-beaker',
            self::Combo => 'heroicon-o-gift',
        };
    }
}
