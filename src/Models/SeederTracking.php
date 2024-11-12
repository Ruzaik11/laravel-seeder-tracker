<?php

namespace Ruzaik11\SeederTracker\Models;

use Illuminate\Database\Eloquent\Model;

class SeederTracking extends Model
{
    protected $table = 'seeder_tracking';
    
    protected $fillable = [
        'seeder_name',
        'executed_at',
        'batch',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public static function hasBeenExecuted(string $seederName): bool
    {
        return static::where('seeder_name', $seederName)->exists();
    }

    public static function getNextBatch(): string
    {
        $lastBatch = static::max('batch');
        return $lastBatch ? (string)((int)$lastBatch + 1) : '1';
    }
}
