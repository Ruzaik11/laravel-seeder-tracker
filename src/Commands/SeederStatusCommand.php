<?php

namespace Ruzaik11\SeederTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ruzaik11\SeederTracker\Models\SeederTracking;

class SeederStatusCommand extends Command
{
    protected $signature = 'seeder:status 
                           {--reset= : Reset specific seeder tracking}
                           {--reset-all : Reset all seeder tracking}
                           {--show-performance : Show performance metrics}';
    
    protected $description = 'Show seeder execution status and manage tracking';

    public function handle()
    {
        if ($this->option('reset-all')) {
            return $this->resetAllSeeders();
        }

        if ($resetSeeder = $this->option('reset')) {
            return $this->resetSpecificSeeder($resetSeeder);
        }

        $this->showSeederStatus();
        
        if ($this->option('show-performance')) {
            $this->showPerformanceMetrics();
        }
    }

    protected function showSeederStatus()
    {
        $executedSeeders = SeederTracking::orderBy('executed_at', 'desc')->get();
        $allSeeders = $this->getAllSeederFiles();

        $this->info('📊 Seeder Execution Status');
        $this->line('');

        if ($allSeeders->isEmpty()) {
            $this->warn('No seeder files found in database/seeders directory.');
            return;
        }

        $headers = ['Seeder', 'Status', 'Executed At', 'Batch', 'Execution Time'];
        $rows = [];

        foreach ($allSeeders as $seeder) {
            $executed = $executedSeeders->firstWhere('seeder_name', $seeder);
            
            if ($executed) {
                $metadata = $executed->metadata ?? [];
                $executionTime = isset($metadata['execution_time_ms']) 
                    ? $metadata['execution_time_ms'] . 'ms' 
                    : 'N/A';

                $rows[] = [
                    class_basename($seeder),
                    '<info>✅ Executed</info>',
                    $executed->executed_at->format('M j, Y H:i'),
                    $executed->batch,
                    $executionTime
                ];
            } else {
                $rows[] = [
                    class_basename($seeder),
                    '<comment>⏳ Pending</comment>',
                    'Not executed',
                    'N/A',
                    'N/A'
                ];
            }
        }

        $this->table($headers, $rows);
        
        $executedCount = $executedSeeders->count();
        $totalCount = $allSeeders->count();
        $this->line('');
        $this->info("Summary: {$executedCount}/{$totalCount} seeders executed");
    }

    protected function showPerformanceMetrics()
    {
        $seeders = SeederTracking::whereJsonContains('metadata->execution_time_ms', '!=', null)
            ->orderBy('executed_at', 'desc')
            ->get();

        if ($seeders->isEmpty()) {
            return;
        }

        $this->line('');
        $this->info('⚡ Performance Metrics');
        $this->line('');

        $totalTime = 0;
        $fastest = null;
        $slowest = null;

        foreach ($seeders as $seeder) {
            $time = $seeder->metadata['execution_time_ms'] ?? 0;
            $totalTime += $time;
            
            if (!$fastest || $time < $fastest['time']) {
                $fastest = ['name' => class_basename($seeder->seeder_name), 'time' => $time];
            }
            
            if (!$slowest || $time > $slowest['time']) {
                $slowest = ['name' => class_basename($seeder->seeder_name), 'time' => $time];
            }
        }

        $avgTime = round($totalTime / $seeders->count(), 2);

        $this->line("🏃 Fastest: {$fastest['name']} ({$fastest['time']}ms)");
        $this->line("🐌 Slowest: {$slowest['name']} ({$slowest['time']}ms)");
        $this->line("📊 Average: {$avgTime}ms");
        $this->line("⏱️  Total time: " . round($totalTime / 1000, 2) . "s");
    }

    protected function getAllSeederFiles()
    {
        $seederPath = database_path('seeders');
        
        if (!File::exists($seederPath)) {
            return collect([]);
        }

        $files = File::glob($seederPath . '/*Seeder.php');
        
        return collect($files)->map(function ($file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            return $className !== 'DatabaseSeeder' ? "Database\\Seeders\\{$className}" : null;
        })->filter()->values();
    }

    protected function resetSpecificSeeder(string $seederName)
    {
        $deleted = SeederTracking::where('seeder_name', 'like', "%{$seederName}%")->delete();

        if ($deleted) {
            $this->info("✅ Reset tracking for seeder: {$seederName}");
        } else {
            $this->error("❌ Seeder not found: {$seederName}");
        }
    }

    protected function resetAllSeeders()
    {
        if ($this->confirm('⚠️  Are you sure you want to reset ALL seeder tracking? This cannot be undone.')) {
            $count = SeederTracking::count();
            SeederTracking::truncate();
            $this->info("✅ Reset tracking for {$count} seeders.");
        } else {
            $this->info('Operation cancelled.');
        }
    }
}
