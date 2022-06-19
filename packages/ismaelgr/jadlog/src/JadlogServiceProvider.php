<?php

namespace ismaelgr\jadlog;

use Illuminate\Support\ServiceProvider;

class JadlogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'/config.php' => config_path('jadlog.php')
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
