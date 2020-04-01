<?php

namespace Mss\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Mss\Models\ArticleGroup;
use Mss\Models\User;

class ArticleGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any article groups.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('article-group.view');
    }

    /**
     * Determine whether the user can view the article group.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\ArticleGroup  $articleGroup
     * @return mixed
     */
    public function view(User $user, ArticleGroup $articleGroup)
    {
        return $user->can('article-group.view');
    }

    /**
     * Determine whether the user can create article groups.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('article-group.create');
    }

    /**
     * Determine whether the user can update the article group.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\ArticleGroup  $articleGroup
     * @return mixed
     */
    public function update(User $user, ArticleGroup $articleGroup)
    {
        return $user->can('article-group.edit');
    }

    /**
     * Determine whether the user can delete the article group.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\ArticleGroup  $articleGroup
     * @return mixed
     */
    public function delete(User $user, ArticleGroup $articleGroup)
    {
        return $user->can('article-group.delete');
    }

    /**
     * Determine whether the user can restore the article group.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\ArticleGroup  $articleGroup
     * @return mixed
     */
    public function restore(User $user, ArticleGroup $articleGroup)
    {
        return $user->can('article-group.delete');
    }

    /**
     * Determine whether the user can permanently delete the article group.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\ArticleGroup  $articleGroup
     * @return mixed
     */
    public function forceDelete(User $user, ArticleGroup $articleGroup)
    {
        return $user->can('article-group.delete');
    }
}
