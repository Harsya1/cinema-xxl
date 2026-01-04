<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\InventoryItem;
use App\Models\User;
use App\Policies\Traits\ChecksUserRole;

/**
 * InventoryItemPolicy - Controls access to Inventory Item management.
 * 
 * Access Matrix:
 * - Admin: Full access
 * - Manager: Read only
 * - Cashier: No access
 * - FnB Staff: Full access
 * - Cleaner: No access
 */
class InventoryItemPolicy
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

    public function view(User $user, InventoryItem $inventoryItem): bool
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

    public function update(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->hasRole($user, [
            UserRole::Admin,
            UserRole::FnbStaff,
        ]);
    }

    public function delete(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
