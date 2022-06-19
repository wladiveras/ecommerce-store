<?php

namespace marcusvbda\uploader;

use Illuminate\Support\ServiceProvider;

class UploaderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations/'),
        ]);
    }

    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('marcusvbda\uploader\Controllers\UploaderController');
    }
}
