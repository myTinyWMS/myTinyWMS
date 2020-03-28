<?php

namespace Mss\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Mss\Models\Supplier;
use Mss\Models\User;

class SupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any suppliers.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('supplier.view');
    }

    /**
     * Determine whether the user can view the supplier.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Supplier  $supplier
     * @return mixed
     */
    public function view(User $user, Supplier $supplier)
    {
        return $user->can('supplier.view');
    }

    /**
     * Determine whether the user can create suppliers.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('supplier.create');
    }

    /**
     * Determine whether the user can update the supplier.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Supplier  $supplier
     * @return mixed
     */
    public function update(User $user, Supplier $supplier)
    {
        return $user->can('supplier.edit');
    }

    /**
     * Determine whether the user can delete the supplier.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Supplier  $supplier
     * @return mixed
     */
    public function delete(User $user, Supplier $supplier)
    {
        return $user->can('supplier.delete');
    }

    /**
     * Determine whether the user can restore the supplier.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Supplier  $supplier
     * @return mixed
     */
    public function restore(User $user, Supplier $supplier)
    {
        return $user->can('supplier.delete');
    }

    /**
     * Determine whether the user can permanently delete the supplier.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Supplier  $supplier
     * @return mixed
     */
    public function forceDelete(User $user, Supplier $supplier)
    {
        return $user->can('supplier.delete');
    }
}
