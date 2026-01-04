<?php

namespace App\Policies\Traits;

use App\Enums\UserRole;
use App\Models\User;

trait ChecksUserRole
{
    /**
     * Check if user is admin.
     */
    protected function isAdmin(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Check if user is manager.
     */
    protected function isManager(User $user): bool
    {
        return $user->role === UserRole::Manager;
    }

    /**
     * Check if user is cashier.
     */
    protected function isCashier(User $user): bool
    {
        return $user->role === UserRole::Cashier;
    }

    /**
     * Check if user is FnB staff.
     */
    protected function isFnbStaff(User $user): bool
    {
        return $user->role === UserRole::FnbStaff;
    }

    /**
     * Check if user is cleaner.
     */
    protected function isCleaner(User $user): bool
    {
        return $user->role === UserRole::Cleaner;
    }

    /**
     * Check if user has any of the given roles.
     */
    protected function hasRole(User $user, array $roles): bool
    {
        return in_array($user->role, $roles, true);
    }

    /**
     * Admin or Manager (management level).
     */
    protected function isManagement(User $user): bool
    {
        return $this->hasRole($user, [UserRole::Admin, UserRole::Manager]);
    }
}
