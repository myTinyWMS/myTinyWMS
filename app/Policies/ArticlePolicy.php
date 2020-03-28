<?php

namespace Mss\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Mss\Models\Article;
use Mss\Models\User;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any articles.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('article.view');
    }

    /**
     * Determine whether the user can view the article.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Article  $article
     * @return mixed
     */
    public function view(User $user, Article $article)
    {
        return $user->can('article.view');
    }

    /**
     * Determine whether the user can create articles.
     *
     * @param  \Mss\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('article.create');
    }

    /**
     * Determine whether the user can update the article.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Article  $article
     * @return mixed
     */
    public function update(User $user, Article $article)
    {
        return $user->can('article.edit');
    }

    /**
     * Determine whether the user can delete the article.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Article  $article
     * @return mixed
     */
    public function delete(User $user, Article $article)
    {
        return $user->can('article.delete');
    }

    /**
     * Determine whether the user can restore the article.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Article  $article
     * @return mixed
     */
    public function restore(User $user, Article $article)
    {
        return $user->can('article.delete');
    }

    /**
     * Determine whether the user can permanently delete the article.
     *
     * @param  \Mss\Models\User  $user
     * @param  \Mss\Models\Article  $article
     * @return mixed
     */
    public function forceDelete(User $user, Article $article)
    {
        return $user->can('article.delete');
    }
}
