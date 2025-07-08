<?php

namespace Ruzaik\SeederTracker;

use Illuminate\Support\ServiceProvider;
use Ruzaik\SeederTracker\Commands\SeederStatusCommand;
use Ruzaik\SeederTracker\Commands\SeederPerformanceCommand;
use Ruzaik\SeederTracker\Commands\SeederCleanupCommand;
use Ruzaik\SeederTracker\Commands\SeederMarkCommand;

class SeederTrackerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/seeder-tracker.php', 'seeder-tracker');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/seeder-tracker.php' => config_path('seeder-tracker.php'),
            ], 'seeder-tracker-config');

            $this->publishes([
                __DIR__ . '/database/migrations/' => database_path('migrations'),
            ], 'seeder-tracker-migrations');

            $this->commands([
                SeederStatusCommand::class,
                SeederPerformanceCommand::class,
                SeederCleanupCommand::class,
                SeederMarkCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
