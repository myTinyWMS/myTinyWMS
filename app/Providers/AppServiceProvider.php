<?php

namespace Mss\Providers;

use Carbon\Carbon;
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

        Relation::morphMap([
            'article' => Article::class,
        ]);

        $this->app->bind('\Webklex\IMAP\Client', function ($app) {
            return Client::account('default');
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
