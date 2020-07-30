<?php

namespace Mss\Providers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Mss\Models\Article;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Webklex\IMAP\Facades\Client;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapThree();

        setlocale(LC_TIME, 'de_DE.utf8');
        date_default_timezone_set('Europe/Berlin');
        Carbon::setLocale('de');

        if (env('APP_DEMO')) {
            config(['mail.driver' => 'array']);
        }

        if (env('APP_ENV') === 'production') {
            $this->app['url']->forceScheme('https');
        }

        Relation::morphMap([
            'article' => Article::class,
        ]);

        Collection::macro('ksort', function(){
            // macros callbacks are bound to collection so we can safely access
            // protected Collection::items
            ksort($this->items);

            return $this;
        });

        Collection::macro('hasNonEmpty', function () {
            return $this->filter(function ($item) {
                return !empty($item);
            })->isNotEmpty();
        });

        if (!empty(env('HORIZON_NOTIFICATION_RECEIVER'))) {
            Horizon::routeMailNotificationsTo(env('HORIZON_NOTIFICATION_RECEIVER'));
        }

        Horizon::auth(function ($request) {
            return Auth::check() && Auth::user()->can('admin');
        });

        if ($this->app->isLocal() && env('DEBUGBAR_ENABLED') !== false) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('APP_ENV') === 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
