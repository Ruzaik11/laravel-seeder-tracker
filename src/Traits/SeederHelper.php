<?php

namespace Ruzaik11\SeederTracker\Traits;

use Illuminate\Support\Facades\DB;

trait SeederHelper
{
    protected function isProduction(): bool
    {
        return app()->environment('production');
    }

    protected function runInEnvironments(array $environments, callable $callback)
    {
        if (in_array(app()->environment(), $environments)) {
            return $callback();
        }

        $this->command->info('Skipped - not running in allowed environments: ' . implode(', ', $environments));
        return null;
    }

    protected function getTableCount(string $table): int
    {
        return DB::table($table)->count();
    }
}
