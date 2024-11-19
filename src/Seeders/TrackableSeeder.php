<?php

namespace Ruzaik11\SeederTracker\Seeders;

use Illuminate\Database\Seeder;
use Ruzaik11\SeederTracker\Contracts\TrackableSeederInterface;
use Ruzaik11\SeederTracker\Models\SeederTracking;
use Ruzaik11\SeederTracker\Traits\PerformanceMonitor;
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
        if ($this->hasBeenExecuted()) {
            $this->command->info("Seeder " . get_class($this) . " already executed. Skipping...");
            return;
        }

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
    }
}
