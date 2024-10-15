<?php

namespace Ruzaik\SeederTracker\Commands;

use Illuminate\Console\Command;
use Ruzaik\SeederTracker\Models\SeederTracking;
use Carbon\Carbon;

class SeederCleanupCommand extends Command
{
    protected $signature = 'seeder:cleanup 
                           {--days= : Number of days to keep records}
                           {--force : Skip confirmation}';
    
    protected $description = 'Clean up old seeder tracking records';

    public function handle()
    {
        $days = $this->option('days') 
            ?? config('seeder-tracker.cleanup_after_days') 
            ?? 30;

        if (!is_numeric($days) || $days < 1) {
            $this->error('Invalid number of days. Must be a positive integer.');
            return 1;
        }

        $cutoffDate = Carbon::now()->subDays($days);
        
        $count = SeederTracking::where('executed_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No old records found to clean up.');
            return 0;
        }

        $this->info("Found {$count} records older than {$days} days.");

        if (!$this->option('force') && !$this->confirm("Delete these {$count} records?")) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        $deleted = SeederTracking::where('executed_at', '<', $cutoffDate)->delete();

        $this->info("✅ Cleaned up {$deleted} old tracking records.");
        
        return 0;
    }
}
