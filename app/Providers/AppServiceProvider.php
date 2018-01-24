<?php

namespace Mss\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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

        date_default_timezone_set('Europe/Berlin');
        Carbon::setLocale('de');
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
