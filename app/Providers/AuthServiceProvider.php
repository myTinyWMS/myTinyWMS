<?php

namespace Mss\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Mss\Models\Article;
use Mss\Models\Inventory;
use Mss\Models\Order;
use Mss\Models\Supplier;
use Mss\Models\User;
use Mss\Policies\ArticlePolicy;
use Mss\Policies\InventoryPolicy;
use Mss\Policies\OrderPolicy;
use Mss\Policies\SupplierPolicy;
use Mss\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Article::class => ArticlePolicy::class,
        Inventory::class => InventoryPolicy::class,
        Order::class => OrderPolicy::class,
        Supplier::class => SupplierPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
    }
}
