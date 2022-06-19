<?php

namespace ismaelgr\getnet;

use Illuminate\Support\ServiceProvider;

class GetnetClassProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('getnet.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->make('ismaelgr\getnet\GetnetClass');
    }
}
