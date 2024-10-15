<?php

namespace Ruzaik\SeederTracker\Traits;

trait PerformanceMonitor
{
    protected $performanceData = [];

    protected function startPerformanceTracking()
    {
        $this->performanceData = [
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(true),
        ];
    }

    protected function endPerformanceTracking()
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $performanceData = [
            'execution_time_ms' => round(($endTime - $this->performanceData['start_time']) * 1000, 2),
            'memory_used_mb' => round(($endMemory - $this->performanceData['start_memory']) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ];

        // Clear performance data to prevent memory leaks
        $this->performanceData = [];

        return $performanceData;
    }

    protected function resetPerformanceData()
    {
        $this->performanceData = [];
        
        // Force garbage collection for long-running seeders
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}
