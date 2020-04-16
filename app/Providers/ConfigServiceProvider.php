<?php

namespace Mss\Providers;

use Illuminate\Support\Facades\Schema;
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
        if (!env('DOCKER_BUILD', false) && Schema::hasTable('settings')) {
            ConfigService::setConfigFromSettings();
        }
    }
}
