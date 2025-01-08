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
        $seederName = get_class($this);
        
        if ($this->hasBeenExecuted() && config('seeder-tracker.prevent_duplicates', true)) {
            $this->command->info("Seeder {$seederName} already executed. Skipping...");
            return;
        }

        $startTime = microtime(true);
        
        try {
            $result = $this->seedData();
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            $tracking = SeederTracking::create([
                'seeder_name' => $seederName,
                'executed_at' => Carbon::now(),
                'batch' => SeederTracking::getNextBatch(),
                'metadata' => [
                    'execution_time_ms' => $executionTime,
                    'result' => $result,
                    'timestamp' => Carbon::now()->toISOString(),
                    'environment' => app()->environment(),
                ],
            ]);

            $this->command->info("✅ Seeder {$seederName} executed successfully in {$executionTime}ms");

        } catch (\Exception $e) {
            // Clean up failed tracking record if it was created
            SeederTracking::where('seeder_name', $seederName)
                ->where('created_at', '>=', Carbon::now()->subMinute())
                ->delete();
                
            $this->command->error("❌ Seeder {$seederName} failed: " . $e->getMessage());
            throw $e;
        }
    }
}
