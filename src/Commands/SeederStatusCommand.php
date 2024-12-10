<?php

namespace Ruzaik11\SeederTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ruzaik11\SeederTracker\Models\SeederTracking;

class SeederStatusCommand extends Command
{
    protected $signature = 'seeder:status 
                           {--reset= : Reset specific seeder tracking}
                           {--reset-all : Reset all seeder tracking}';
    
    protected $description = 'Show seeder execution status';

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

        $this->info('Seeder Status:');
        $this->line('');

        $headers = ['Seeder', 'Status', 'Executed At', 'Batch'];
        $rows = [];

        foreach ($allSeeders as $seeder) {
            $executed = $executedSeeders->firstWhere('seeder_name', $seeder);
            
            if ($executed) {
                $rows[] = [
                    $seeder,
                    '<info>✓ Executed</info>',
                    $executed->executed_at->format('Y-m-d H:i:s'),
                    $executed->batch
                ];
            } else {
                $rows[] = [
                    $seeder,
                    '<comment>○ Pending</comment>',
                    'N/A',
                    'N/A'
                ];
            }
        }

        $this->table($headers, $rows);
    }

    protected function getAllSeederFiles(): array
    {
        $seederPath = database_path('seeders');
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
            $this->info("Reset tracking for seeder: {$seederName}");
        } else {
            $this->error("Seeder not found: {$seederName}");
        }
    }

    protected function resetAllSeeders()
    {
        if ($this->confirm('Are you sure you want to reset all seeder tracking?')) {
            SeederTracking::truncate();
            $this->info('All seeder tracking has been reset.');
        }
    }
}
