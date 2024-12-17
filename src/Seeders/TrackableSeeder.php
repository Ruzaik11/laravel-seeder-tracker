<?php

namespace Ruzaik11\SeederTracker\Seeders;

use Illuminate\Database\Seeder;
use Ruzaik11\SeederTracker\Contracts\TrackableSeederInterface;
use Ruzaik11\SeederTracker\Models\SeederTracking;
use Carbon\Carbon;

abstract class TrackableSeeder extends Seeder implements TrackableSeederInterface
{
    public function hasBeenExecuted(): bool
    {
        return SeederTracking::hasBeenExecuted(get_class($this));
    }

    public function resetTracking(): void
    {
        SeederTracking::where('seeder_name', get_class($this))->delete();
    }

    abstract public function seedData();

    public function run()
    {
        if ($this->hasBeenExecuted() && config('seeder-tracker.prevent_duplicates', true)) {
            $this->command->info("Seeder " . get_class($this) . " already executed. Skipping...");
            return;
        }

        $startTime = microtime(true);
        
        try {
            $result = $this->seedData();
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            SeederTracking::create([
                'seeder_name' => get_class($this),
                'executed_at' => Carbon::now(),
                'batch' => SeederTracking::getNextBatch(),
                'metadata' => [
                    'execution_time_ms' => $executionTime,
                    'result' => $result,
                    'timestamp' => Carbon::now()->toISOString()
                ],
            ]);

            $this->command->info("Seeder " . get_class($this) . " executed successfully in {$executionTime}ms");

        } catch (\Exception $e) {
            $this->command->error("Seeder " . get_class($this) . " failed: " . $e->getMessage());
            throw $e;
        }
    }
}
