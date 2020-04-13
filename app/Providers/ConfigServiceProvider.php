<?php

namespace Mss\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Mss\Services\ConfigService;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        // set config only if not during docker build - there is no DB connection at this time and this fails
        if (!env('DOCKER_BUILD', false) && env('APP_ENV') != 'testing') {
            ConfigService::setConfigFromSettings();
        }
    }
}
