<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Showtime;
use App\Models\User;
use App\Policies\Traits\ChecksUserRole;

/**
 * MoviePolicy - Controls access to Movie/Showtime browsing features.
 * 
 * Access Matrix:
 * - Admin: Full access
 * - Manager: Read only
 * - Cashier: Read only (need to see movies for POS)
 * - FnB Staff: No access
 * - Cleaner: No access
 */
class ShowtimePolicy
{
    use ChecksUserRole;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::Cashier,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Showtime $showtime): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Showtime $showtime): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Showtime $showtime): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
