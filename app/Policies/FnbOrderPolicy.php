<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\FnbOrder;
use App\Models\User;
use App\Policies\Traits\ChecksUserRole;

/**
 * FnbOrderPolicy - Controls access to F&B Order management.
 * 
 * Access Matrix:
 * - Admin: Full access
 * - Manager: Read only
 * - Cashier: No access
 * - FnB Staff: Full access
 * - Cleaner: No access
 */
class FnbOrderPolicy
{
    use ChecksUserRole;

    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::FnbStaff,
        ]);
    }

    public function view(User $user, FnbOrder $fnbOrder): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::FnbStaff,
        ]);
    }

    public function update(User $user, FnbOrder $fnbOrder): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::FnbStaff,
        ]);
    }

    public function delete(User $user, FnbOrder $fnbOrder): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
