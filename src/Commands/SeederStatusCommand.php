<?php

namespace Ruzaik11\SeederTracker\Commands;

use Illuminate\Console\Command;
use Ruzaik11\SeederTracker\Models\SeederTracking;

class SeederStatusCommand extends Command
{
    protected $signature = 'seeder:status';
    protected $description = 'Show seeder execution status';

    public function handle()
    {
        $seeders = SeederTracking::orderBy('executed_at', 'desc')->get();
        
        if ($seeders->isEmpty()) {
            $this->info('No seeders have been executed yet.');
            return;
        }

        $headers = ['Seeder', 'Executed At', 'Batch'];
        $rows = [];

        foreach ($seeders as $seeder) {
            $rows[] = [
                $seeder->seeder_name,
                $seeder->executed_at->format('Y-m-d H:i:s'),
                $seeder->batch,
            ];
        }

        $this->table($headers, $rows);
    }
}
