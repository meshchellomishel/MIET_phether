<?php

namespace Ensi\QueryBuilderHelpers;

use Illuminate\Support\ServiceProvider;

class QueryBuilderHelpersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/query-builder-helpers.php' => config_path('query-builder-helpers.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/query-builder-helpers.php', 'query-builder-helpers');
    }
}
