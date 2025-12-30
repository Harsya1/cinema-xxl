<?php

namespace App\Enums;

enum StudioType: string
{
    case Regular = 'Regular';
    case Premier = 'Premier';
    case ThreeD = '3D';

    public function label(): string
    {
        return match ($this) {
            self::Regular => 'Regular',
            self::Premier => 'Premier',
            self::ThreeD => '3D',
        };
    }

    public function priceMultiplier(): float
    {
        return match ($this) {
            self::Regular => 1.0,
            self::Premier => 1.5,
            self::ThreeD => 1.3,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Regular => 'gray',
            self::Premier => 'warning',
            self::ThreeD => 'info',
        };
    }
}
