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

        $this->startPerformanceTracking();
        
        try {
            $result = $this->seedData();
            
            $performanceData = $this->endPerformanceTracking();
            
            // Merge performance data with result data
            $metadata = array_merge($performanceData, [
                'result' => $result,
                'timestamp' => Carbon::now()->toISOString(),
                'environment' => app()->environment(),
            ]);
            
            SeederTracking::create([
                'seeder_name' => $seederName,
                'executed_at' => Carbon::now(),
                'batch' => SeederTracking::getNextBatch(),
                'metadata' => $metadata,
            ]);

            $this->command->info("✅ Seeder {$seederName} executed successfully in {$performanceData['execution_time_ms']}ms");

        } catch (\Exception $e) {
            // Clean up failed tracking record if it was created
            SeederTracking::where('seeder_name', $seederName)
                ->where('created_at', '>=', Carbon::now()->subMinute())
                ->delete();
                
            $this->command->error("❌ Seeder {$seederName} failed: " . $e->getMessage());
            throw $e;
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
}