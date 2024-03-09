<?php

namespace Ensi\LaravelEnsiFilesystem;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class EnsiFilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ensi.filesystem', function (Application $app) {
            return new EnsiFilesystemManager($app);
        });
        $this->app->alias('ensi.filesystem', EnsiFilesystemManager::class);

        $this->mergeConfigFrom(__DIR__.'/../config/ensi-filesystem.php', 'ensi-filesystem');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ensi-filesystem.php' => config_path('ensi-filesystem.php'),
            ], 'config');
        }
    }
}
