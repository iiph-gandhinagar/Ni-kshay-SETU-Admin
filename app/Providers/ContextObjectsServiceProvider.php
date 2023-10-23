<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ContextObjectsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(\App\Http\ViewComposers\MainLayoutViewComposer::class);
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            '*', 'App\Http\ViewComposers\MainLayoutViewComposer'
        );
    }
}
