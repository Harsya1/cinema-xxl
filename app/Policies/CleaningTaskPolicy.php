<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CleaningTask;
use App\Models\User;
use App\Policies\Traits\ChecksUserRole;

/**
 * CleaningTaskPolicy - Controls access to Cleaning Task management.
 * 
 * Access Matrix:
 * - Admin: Full access
 * - Manager: Read only
 * - Cashier: No access
 * - FnB Staff: No access
 * - Cleaner: Can view and update status of their assigned tasks
 */
class CleaningTaskPolicy
{
    use ChecksUserRole;

    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::Cleaner,
        ]);
    }

    public function view(User $user, CleaningTask $cleaningTask): bool
    {
        // Cleaner can only view their assigned tasks
        if ($this->isCleaner($user)) {
            return $cleaningTask->cleaner_id === $user->id || $cleaningTask->cleaner_id === null;
        }
        
        return $this->isManagement($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, CleaningTask $cleaningTask): bool
    {
        // Admin can update anything
        if ($this->isAdmin($user)) {
            return true;
        }
        
        // Cleaner can only update status of their assigned tasks
        if ($this->isCleaner($user)) {
            return $cleaningTask->cleaner_id === $user->id;
        }
        
        return false;
    }

    public function delete(User $user, CleaningTask $cleaningTask): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
