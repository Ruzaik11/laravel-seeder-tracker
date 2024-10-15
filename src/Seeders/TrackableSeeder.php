<?php

namespace Ruzaik\SeederTracker\Seeders;

use Illuminate\Database\Seeder;
use Ruzaik\SeederTracker\Contracts\TrackableSeederInterface;
use Ruzaik\SeederTracker\Models\SeederTracking;
use Ruzaik\SeederTracker\Traits\PerformanceMonitor;
use Carbon\Carbon;

abstract class TrackableSeeder extends Seeder implements TrackableSeederInterface
{
    use PerformanceMonitor;

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

<<<<<<< HEAD
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
=======
        $this->startPerformanceTracking();
        
        $this->seedData();
        
        $performanceData = $this->endPerformanceTracking();
        
        SeederTracking::create([
            'seeder_name' => get_class($this),
            'executed_at' => Carbon::now(),
            'batch' => SeederTracking::getNextBatch(),
            'metadata' => $performanceData,
        ]);

        $this->command->info("Seeder executed in {$performanceData['execution_time_ms']}ms");
>>>>>>> feature/performance-monitoring
    }
}

    /**
     * Check if we should force run regardless of tracking
     */
    protected function shouldForceRun(): bool
    {
        return !config('seeder-tracker.prevent_duplicates', true) ||
               !in_array(app()->environment(), config('seeder-tracker.strict_environments', []));
    }
