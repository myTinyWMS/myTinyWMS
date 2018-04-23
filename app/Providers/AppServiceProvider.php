<?php

namespace Mss\Providers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mss\Models\Article;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Webklex\IMAP\Facades\Client;

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
