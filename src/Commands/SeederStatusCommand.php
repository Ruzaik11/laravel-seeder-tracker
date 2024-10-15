<?php

namespace Ruzaik\SeederTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ruzaik\SeederTracker\Models\SeederTracking;

class SeederStatusCommand extends Command
{
    protected $signature = 'seeder:status 
                           {--reset= : Reset specific seeder tracking}
                           {--reset-all : Reset all seeder tracking}
                           {--detailed : Show detailed information}';
    
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
    }

    protected function showSeederStatus()
    {
        $executedSeeders = SeederTracking::orderBy('executed_at', 'desc')->get();
        $allSeeders = $this->getAllSeederFiles();

        $this->info('📊 Seeder Execution Status');
        $this->line('');

        $headers = $this->option('detailed') 
            ? ['Seeder', 'Status', 'Executed At', 'Batch', 'Execution Time', 'Memory Used']
            : ['Seeder', 'Status', 'Executed At', 'Batch'];
        $rows = [];

        foreach ($allSeeders as $seeder) {
            $executed = $executedSeeders->firstWhere('seeder_name', $seeder);
            
            if ($executed) {
                $metadata = $executed->metadata ?? [];
                $row = [
                    class_basename($seeder),
                    '<info>✅ Executed</info>',
                    $executed->executed_at->format('M j, Y H:i'),
                    $executed->batch
                ];

                if ($this->option('detailed')) {
                    $row[] = isset($metadata['execution_time_ms']) ? $metadata['execution_time_ms'] . 'ms' : 'N/A';
                    $row[] = isset($metadata['memory_used_mb']) ? $metadata['memory_used_mb'] . 'MB' : 'N/A';
                }

                $rows[] = $row;
            } else {
                $row = [
                    class_basename($seeder),
                    '<comment>⏳ Pending</comment>',
                    'Not executed',
                    'N/A'
                ];

                if ($this->option('detailed')) {
                    $row[] = 'N/A';
                    $row[] = 'N/A';
                }

                $rows[] = $row;
            }
        }

        $this->table($headers, $rows);
        
        $executedCount = $executedSeeders->count();
        $totalCount = count($allSeeders);
        $this->line('');
        $this->info("Summary: {$executedCount}/{$totalCount} seeders executed");
    }

    protected function getAllSeederFiles(): array
    {
        $seederPath = database_path('seeders');
        
        if (!File::exists($seederPath)) {
            return [];
        }

        $files = File::glob($seederPath . '/*Seeder.php');
        
        $seeders = [];
        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            if ($className !== 'DatabaseSeeder') {
                $seeders[] = "Database\\Seeders\\{$className}";
            }
        }

        return $seeders;
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
        if ($this->confirm('⚠️  Are you sure you want to reset ALL seeder tracking?')) {
            $count = SeederTracking::count();
            SeederTracking::truncate();
            $this->info("✅ Reset tracking for {$count} seeders.");
        } else {
            $this->info('Operation cancelled.');
        }
    }
}
