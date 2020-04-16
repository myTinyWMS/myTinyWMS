<?php

namespace Mss\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
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
        if (DB::connection()->getDatabaseName() && DB::table('settings')->exists()) {
            ConfigService::setConfigFromSettings();
        }
    }
}
