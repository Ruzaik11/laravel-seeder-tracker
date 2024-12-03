<?php

namespace Ruzaik11\SeederTracker;

use Illuminate\Support\ServiceProvider;
use Ruzaik11\SeederTracker\Commands\SeederStatusCommand;

class SeederTrackerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/seeder-tracker.php', 'seeder-tracker');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/seeder-tracker.php' => config_path('seeder-tracker.php'),
            ], 'seeder-tracker-config');

            $this->publishes([
                __DIR__.'/database/migrations/' => database_path('migrations'),
            ], 'seeder-tracker-migrations');

            $this->commands([
                SeederStatusCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
