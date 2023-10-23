<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;

use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (App::environment('production')  || App::environment('staging')) {
            $this->app['request']->server->set('HTTPS','on');
            URL::forceScheme('https');
        }
    }
}
