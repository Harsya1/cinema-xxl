<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Qris = 'qris';
    case Transfer = 'transfer';
    case EWallet = 'e-wallet';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Card => 'Credit/Debit Card',
            self::Qris => 'QRIS',
            self::Transfer => 'Bank Transfer',
            self::EWallet => 'E-Wallet',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash => 'heroicon-o-banknotes',
            self::Card => 'heroicon-o-credit-card',
            self::Qris => 'heroicon-o-qr-code',
            self::Transfer => 'heroicon-o-building-library',
            self::EWallet => 'heroicon-o-device-phone-mobile',
        };
    }
}
