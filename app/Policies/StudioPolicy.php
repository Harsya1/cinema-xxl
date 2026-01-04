<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Studio;
use App\Models\User;
use App\Policies\Traits\ChecksUserRole;

/**
 * StudioPolicy - Controls access to Studio management.
 * 
 * Access Matrix:
 * - Admin: Full access
 * - Manager: Read only
 * - Cashier: Read only
 * - FnB Staff: No access
 * - Cleaner: Read only (to see studio status for cleaning)
 */
class StudioPolicy
{
    use ChecksUserRole;

    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::Cashier,
            UserRole::Cleaner,
        ]);
    }

    public function view(User $user, Studio $studio): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Studio $studio): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Studio $studio): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
