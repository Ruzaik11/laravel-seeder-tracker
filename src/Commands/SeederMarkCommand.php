<?php

namespace Ruzaik\SeederTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ruzaik\SeederTracker\Models\SeederTracking;
use Carbon\Carbon;

class SeederMarkCommand extends Command
{
    protected $signature = 'seeder:mark
                           {--all : Mark all seeders as executed}
                           {--seeder= : Mark specific seeder as executed}
                           {--batch= : Specify batch number (defaults to next available)}
                           {--force : Skip confirmation}
                           {--list : List all available seeders}';

    protected $description = 'Mark seeders as executed without running them';

    public function handle()
    {
        if ($this->option('list')) {
            return $this->listAvailableSeeders();
        }

        if ($this->option('all')) {
            return $this->markAllSeeders();
        }

        if ($seederName = $this->option('seeder')) {
            return $this->markSpecificSeeder($seederName);
        }

        $this->error('Please specify --all, --seeder=SeederName, or --list');
        return 1;
    }

    protected function listAvailableSeeders()
    {
        $allSeeders = $this->getAllSeederFiles();
        $executedSeeders = SeederTracking::pluck('seeder_name')->toArray();

        $this->info('📋 Available Seeders:');
        $this->line('');

        $headers = ['Seeder', 'Status', 'File Path'];
        $rows = [];

        foreach ($allSeeders as $seeder) {
            $isExecuted = in_array($seeder['class'], $executedSeeders);
            $rows[] = [
                $seeder['name'],
                $isExecuted ? '<info>✅ Executed</info>' : '<comment>⏳ Pending</comment>',
                $seeder['file']
            ];
        }

        $this->table($headers, $rows);

        $pendingCount = count(array_filter($allSeeders, function ($seeder) use ($executedSeeders) {
            return !in_array($seeder['class'], $executedSeeders);
        }));

        $this->line('');
        $this->info("Total: " . count($allSeeders) . " seeders, {$pendingCount} pending");
    }

    protected function markAllSeeders()
    {
        $allSeeders = $this->getAllSeederFiles();
        $executedSeeders = SeederTracking::pluck('seeder_name')->toArray();

        $pendingSeeders = array_filter($allSeeders, function ($seeder) use ($executedSeeders) {
            return !in_array($seeder['class'], $executedSeeders);
        });

        if (empty($pendingSeeders)) {
            $this->info('All seeders are already marked as executed.');
            return 0;
        }

        $this->info('Found ' . count($pendingSeeders) . ' pending seeders:');
        foreach ($pendingSeeders as $seeder) {
            $this->line("  - {$seeder['name']}");
        }
        $this->line('');

        if (!$this->option('force') && !$this->confirm('Mark all these seeders as executed?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $batch = $this->getBatchNumber();
        $marked = 0;

        foreach ($pendingSeeders as $seeder) {
            $this->markSeederAsExecuted($seeder['class'], $batch);
            $marked++;
        }

        $this->info("✅ Marked {$marked} seeders as executed in batch {$batch}");
        return 0;
    }

    protected function markSpecificSeeder(string $seederName)
    {
        $allSeeders = $this->getAllSeederFiles();

        // Find seeder by name (case-insensitive, partial match)
        $matchingSeeders = array_filter($allSeeders, function ($seeder) use ($seederName) {
            return stripos($seeder['name'], $seederName) !== false ||
                stripos($seeder['class'], $seederName) !== false;
        });

        if (empty($matchingSeeders)) {
            $this->error("Seeder not found: {$seederName}");
            $this->line('Use --list to see available seeders.');
            return 1;
        }

        if (count($matchingSeeders) > 1) {
            $this->error("Multiple seeders match '{$seederName}':");
            foreach ($matchingSeeders as $seeder) {
                $this->line("  - {$seeder['name']}");
            }
            $this->line('Please be more specific.');
            return 1;
        }

        $seeder = reset($matchingSeeders);

        if (SeederTracking::hasBeenExecuted($seeder['class'])) {
            $this->warn("Seeder {$seeder['name']} is already marked as executed.");
            if (!$this->option('force') && !$this->confirm('Mark it again anyway?')) {
                return 0;
            }
        }

        $batch = $this->getBatchNumber();
        $this->markSeederAsExecuted($seeder['class'], $batch);

        $this->info("✅ Marked {$seeder['name']} as executed in batch {$batch}");
        return 0;
    }

    protected function markSeederAsExecuted(string $seederClass, string $batch)
    {
        SeederTracking::updateOrCreate(
            ['seeder_name' => $seederClass],
            [
                'executed_at' => Carbon::now(),
                'batch' => $batch,
                'metadata' => [
                    'marked_manually' => true,
                    'marked_at' => Carbon::now()->toISOString(),
                    'marked_by_command' => true,
                    'environment' => app()->environment(),
                    'execution_time_ms' => 0,
                    'memory_used_mb' => 0,
                ],
            ]
        );
    }

    protected function getBatchNumber(): string
    {
        if ($batch = $this->option('batch')) {
            return (string)$batch;
        }

        return SeederTracking::getNextBatch();
    }

    protected function getAllSeederFiles(): array
    {
        $seederPaths = [
            database_path('seeders'),
            base_path('database/seeders'), // Alternative path
        ];

        $seeders = [];

        foreach ($seederPaths as $seederPath) {
            if (!File::exists($seederPath)) {
                continue;
            }

            $files = File::glob($seederPath . '/*Seeder.php');

            foreach ($files as $file) {
                $className = pathinfo($file, PATHINFO_FILENAME);

                // Skip DatabaseSeeder
                if ($className === 'DatabaseSeeder') {
                    continue;
                }

                $seeders[] = [
                    'name' => $className,
                    'class' => "Database\\Seeders\\{$className}",
                    'file' => $file,
                ];
            }
        }

        return $seeders;
    }
}
