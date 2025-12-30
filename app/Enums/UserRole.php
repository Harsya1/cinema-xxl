<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Admin = 'admin';
    case Manager = 'manager';
    case Cashier = 'cashier';
    case FnbStaff = 'fnb_staff';
    case Cleaner = 'cleaner';

    public function label(): string
    {
        return match ($this) {
            self::User => 'User',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Cashier => 'Cashier',
            self::FnbStaff => 'FnB Staff',
            self::Cleaner => 'Cleaner',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::User => 'gray',
            self::Admin => 'danger',
            self::Manager => 'warning',
            self::Cashier => 'success',
            self::FnbStaff => 'info',
            self::Cleaner => 'primary',
        };
    }

    public static function staffRoles(): array
    {
        return [
            self::Admin,
            self::Manager,
            self::Cashier,
            self::FnbStaff,
            self::Cleaner,
        ];
    }
}
