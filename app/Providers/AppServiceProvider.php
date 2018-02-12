<?php

namespace Mss\Providers;

use Carbon\Carbon;
use Mss\Models\Article;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
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
        Schema::defaultStringLength(191);

        Paginator::useBootstrapThree();

        date_default_timezone_set('Europe/Berlin');
        Carbon::setLocale('de');

        Relation::morphMap([
            'article' => Article::class,
        ]);

        Builder::macro('addSubSelect', function ($column, $query) {
            if (is_null($this->getQuery()->columns)) {
                $this->select($this->getQuery()->from.'.*');
            }
            return $this->selectSub($query->limit(1)->getQuery(), $column);
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
