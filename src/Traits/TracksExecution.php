<?php

namespace Ruzaik\SeederTracker\Traits;

use Ruzaik\SeederTracker\Models\SeederTracking;
use Carbon\Carbon;

trait TracksExecution
{
    use PerformanceMonitor;

    /**
     * Original run method that adds tracking functionality
     */
    public function run()
    {
        $seederName = get_class($this);

        // Check if seeder has already been executed
        if ($this->hasBeenExecuted() && config('seeder-tracker.prevent_duplicates', true)) {
            $this->command->info("Seeder {$seederName} already executed. Skipping...");
            return;
        }

        $this->startPerformanceTracking();

        try {
            // Call the original seeder logic
            $this->executeSeederLogic();

            $performanceData = $this->endPerformanceTracking();

            // Create tracking record
            $this->createTrackingRecord($performanceData);

            $this->command->info("✅ Seeder {$seederName} executed successfully in {$performanceData['execution_time_ms']}ms");
        } catch (\Exception $e) {
            // Clean up any failed tracking records
            $this->cleanupFailedTracking($seederName);

            $this->command->error("❌ Seeder {$seederName} failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Execute the actual seeder logic - override this method in your seeder
     */
    protected function executeSeederLogic()
    {
        // If the seeder has a seedData method (like TrackableSeeder), call it
        if (method_exists($this, 'seedData')) {
            return $this->seedData();
        }

        // Otherwise, this method should be overridden in the seeder class
        throw new \BadMethodCallException(
            'You must override the executeSeederLogic() method in your seeder class when using TracksExecution trait, ' .
                'or implement a seedData() method.'
        );
    }

    /**
     * Check if this seeder has been executed before
     */
    public function hasBeenExecuted(): bool
    {
        return SeederTracking::hasBeenExecuted(get_class($this));
    }

    /**
     * Reset tracking for this seeder
     */
    public function resetTracking(): void
    {
        SeederTracking::where('seeder_name', get_class($this))->delete();
    }

    /**
     * Create the tracking record with metadata
     */
    protected function createTrackingRecord(array $performanceData, array $customMetadata = []): void
    {
        $metadata = array_merge($performanceData, $customMetadata, [
            'timestamp' => Carbon::now()->toISOString(),
            'environment' => app()->environment(),
        ]);

        SeederTracking::create([
            'seeder_name' => get_class($this),
            'executed_at' => Carbon::now(),
            'batch' => SeederTracking::getNextBatch(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Clean up failed tracking records
     */
    protected function cleanupFailedTracking(string $seederName): void
    {
        SeederTracking::where('seeder_name', $seederName)
            ->where('created_at', '>=', Carbon::now()->subMinute())
            ->delete();
    }

    /**
     * Track seeder execution with custom metadata
     */
    protected function trackWithMetadata(callable $callback, array $metadata = []): mixed
    {
        $result = $callback();

        // If we're currently tracking performance, add the metadata
        if (!empty($this->performanceData)) {
            $performanceData = $this->endPerformanceTracking();
            $this->createTrackingRecord($performanceData, array_merge($metadata, ['result' => $result]));
        }

        return $result;
    }
}
