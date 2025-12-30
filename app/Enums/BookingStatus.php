<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Booked = 'booked';
    case Paid = 'paid';
    case Redeemed = 'redeemed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Booked => 'Booked',
            self::Paid => 'Paid',
            self::Redeemed => 'Redeemed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Booked => 'warning',
            self::Paid => 'success',
            self::Redeemed => 'info',
            self::Cancelled => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Booked => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-circle',
            self::Redeemed => 'heroicon-o-ticket',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }
}
