<?php

namespace Ruzaik\SeederTracker\Commands;

use Illuminate\Console\Command;
use Ruzaik\SeederTracker\Models\SeederTracking;

class SeederPerformanceCommand extends Command
{
    protected $signature = 'seeder:performance {--limit=10 : Number of results to show}';
    protected $description = 'Show seeder performance analytics';

    public function handle()
    {
        $this->showPerformanceSummary();
        $this->showSlowestSeeders();
        $this->showFastestSeeders();
    }

    protected function showPerformanceSummary()
    {
        $seeders = SeederTracking::whereNotNull('metadata')->get();
        
        if ($seeders->isEmpty()) {
            $this->warn('No performance data available.');
            return;
        }

        $totalTime = 0;
        $totalMemory = 0;
        $count = 0;

        foreach ($seeders as $seeder) {
            $metadata = $seeder->metadata;
            if (isset($metadata['execution_time_ms'])) {
                $totalTime += $metadata['execution_time_ms'];
                $count++;
            }
            if (isset($metadata['memory_used_mb'])) {
                $totalMemory += $metadata['memory_used_mb'];
            }
        }

        $this->info('📊 Performance Summary');
        $this->line('');
        $this->line("Total executions: {$count}");
        $this->line("Average execution time: " . round($totalTime / $count, 2) . "ms");
        $this->line("Total time spent: " . round($totalTime / 1000, 2) . "s");
        $this->line("Average memory usage: " . round($totalMemory / $count, 2) . "MB");
        $this->line('');
    }

    protected function showSlowestSeeders()
    {
        $slowest = SeederTracking::whereNotNull('metadata')
            ->get()
            ->sortByDesc(function ($seeder) {
                return $seeder->metadata['execution_time_ms'] ?? 0;
            })
            ->take($this->option('limit'));

        if ($slowest->isEmpty()) return;

        $this->info('🐌 Slowest Seeders');
        $headers = ['Seeder', 'Execution Time', 'Memory Used', 'Executed At'];
        $rows = [];

        foreach ($slowest as $seeder) {
            $metadata = $seeder->metadata;
            $rows[] = [
                class_basename($seeder->seeder_name),
                ($metadata['execution_time_ms'] ?? 0) . 'ms',
                ($metadata['memory_used_mb'] ?? 0) . 'MB',
                $seeder->executed_at->format('M j, H:i')
            ];
        }

        $this->table($headers, $rows);
        $this->line('');
    }

    protected function showFastestSeeders()
    {
        $fastest = SeederTracking::whereNotNull('metadata')
            ->get()
            ->sortBy(function ($seeder) {
                return $seeder->metadata['execution_time_ms'] ?? PHP_INT_MAX;
            })
            ->take($this->option('limit'));

        if ($fastest->isEmpty()) return;

        $this->info('🏃 Fastest Seeders');
        $headers = ['Seeder', 'Execution Time', 'Memory Used', 'Executed At'];
        $rows = [];

        foreach ($fastest as $seeder) {
            $metadata = $seeder->metadata;
            $rows[] = [
                class_basename($seeder->seeder_name),
                ($metadata['execution_time_ms'] ?? 0) . 'ms',
                ($metadata['memory_used_mb'] ?? 0) . 'MB',
                $seeder->executed_at->format('M j, H:i')
            ];
        }

        $this->table($headers, $rows);
    }
}
