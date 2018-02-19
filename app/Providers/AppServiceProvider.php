<?php

namespace Mss\Providers;

use Carbon\Carbon;
use Mss\Models\Article;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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

        setlocale(LC_TIME, 'German');
        date_default_timezone_set('Europe/Berlin');
        Carbon::setLocale('de');

        Relation::morphMap([
            'article' => Article::class,
        ]);
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
