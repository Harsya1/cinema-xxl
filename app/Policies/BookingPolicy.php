<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use App\Policies\Traits\ChecksUserRole;

/**
 * BookingPolicy - Controls access to Booking management.
 * 
 * Access Matrix:
 * - Admin: Full access
 * - Manager: Read only
 * - Cashier: Create and Read (for POS)
 * - FnB Staff: No access
 * - Cleaner: No access
 */
class BookingPolicy
{
    use ChecksUserRole;

    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::Cashier,
        ]);
    }

    public function view(User $user, Booking $booking): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::Cashier,
        ]);
    }

    public function update(User $user, Booking $booking): bool
    {
        // Admin can update any, Cashier can update status (mark paid, cancel)
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::Cashier,
        ]);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
