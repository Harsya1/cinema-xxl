<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\Traits\ChecksUserRole;

/**
 * UserPolicy - Controls access to User management.
 * 
 * Access Matrix:
 * - Admin: Full access
 * - Manager: Read only
 * - Others: No access
 */
class UserPolicy
{
    use ChecksUserRole;

    public function viewAny(User $user): bool
    {
        return $this->isManagement($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isManagement($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, User $model): bool
    {
        // Admin can update any user
        // Users can update their own profile (handled elsewhere)
        return $this->isAdmin($user);
    }

    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }
        
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
