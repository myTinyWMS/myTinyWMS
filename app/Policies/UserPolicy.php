<?php

namespace Mss\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Mss\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->can('manage users');
    }
}
