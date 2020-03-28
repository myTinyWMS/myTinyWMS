<?php

namespace Mss\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Mss\Models\Inventory;
use Mss\Models\User;

class InventoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any inventories.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('inventory.view');
    }

    /**
     * Determine whether the user can view the inventory.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Inventory  $inventory
     * @return mixed
     */
    public function view(User $user, Inventory $inventory)
    {
        return $user->can('inventory.view');
    }

    /**
     * Determine whether the user can create inventories.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('inventory.create');
    }

    /**
     * Determine whether the user can update the inventory.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Inventory  $inventory
     * @return mixed
     */
    public function update(User $user, Inventory $inventory)
    {
        return $user->can('inventory.edit');
    }

    /**
     * Determine whether the user can delete the inventory.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Inventory  $inventory
     * @return mixed
     */
    public function delete(User $user, Inventory $inventory)
    {
        return $user->can('inventory.delete');
    }

    /**
     * Determine whether the user can restore the inventory.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Inventory  $inventory
     * @return mixed
     */
    public function restore(User $user, Inventory $inventory)
    {
        return $user->can('inventory.delete');
    }

    /**
     * Determine whether the user can permanently delete the inventory.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Inventory  $inventory
     * @return mixed
     */
    public function forceDelete(User $user, Inventory $inventory)
    {
        return $user->can('inventory.delete');
    }
}
