<?php

namespace Mss\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Mss\Models\Order;
use Mss\Models\User;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any orders.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('order.view');
    }

    /**
     * Determine whether the user can view the order.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Order  $order
     * @return mixed
     */
    public function view(User $user, Order $order)
    {
        return $user->can('order.view');
    }

    /**
     * Determine whether the user can create orders.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('order.create');
    }

    /**
     * Determine whether the user can update the order.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Order  $order
     * @return mixed
     */
    public function update(User $user, Order $order)
    {
        return $user->can('order.edit');
    }

    /**
     * Determine whether the user can delete the order.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Order  $order
     * @return mixed
     */
    public function delete(User $user, Order $order)
    {
        return $user->can('order.delete');
    }

    /**
     * Determine whether the user can restore the order.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Order  $order
     * @return mixed
     */
    public function restore(User $user, Order $order)
    {
        return $user->can('order.delete');
    }

    /**
     * Determine whether the user can permanently delete the order.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Order  $order
     * @return mixed
     */
    public function forceDelete(User $user, Order $order)
    {
        return $user->can('order.delete');
    }
}
