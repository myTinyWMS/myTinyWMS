<?php

namespace Mss\Providers;

use Illuminate\Support\ServiceProvider;
use Mss\Models\ArticleQuantityChangelog;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('changelogtype', function ($attribute, $value, $parameters, $validator) {
            /* @var $validator \Illuminate\Validation\Validator */
            $changeType = $validator->getData()['changelogType'];

            if ($changeType == ArticleQuantityChangelog::TYPE_INCOMING && $value === 'sub') {
                return false;
            } elseif ($changeType == ArticleQuantityChangelog::TYPE_OUTGOING && $value === 'add') {
                return false;
            }

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
