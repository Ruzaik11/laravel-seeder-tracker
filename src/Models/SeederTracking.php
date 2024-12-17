<?php

namespace Ruzaik11\SeederTracker\Models;

use Illuminate\Database\Eloquent\Model;

class SeederTracking extends Model
{
    protected $fillable = [
        'seeder_name',
        'executed_at',
        'batch',
        'metadata',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('seeder-tracker.table', 'seeder_tracking'));
    }

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
