<?php
namespace Mss\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory as ViewFactory;

class ComposerServiceProvider extends ServiceProvider {

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot(ViewFactory $view)
    {
        $view->composer('*', 'Mss\Http\ViewComposers\GlobalComposer');
    }

    public function register()
    {
        //
    }

}