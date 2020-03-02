<?php

namespace Mss\Providers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
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

        if (env('APP_ENV') === 'production') {
            $this->app['url']->forceScheme('https');
        }

        Relation::morphMap([
            'article' => Article::class,
        ]);

        $this->app->bind('\Webklex\IMAP\Client', function ($app) {
            return Client::account('default');
        });

        Collection::macro('ksort', function(){
            //macros callbacks are bound to collection so we can safely access
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
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
