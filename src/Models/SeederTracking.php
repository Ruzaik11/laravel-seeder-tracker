<?php

namespace Ruzaik11\SeederTracker\Models;

use Illuminate\Database\Eloquent\Model;

class SeederTracking extends Model
{
    protected $table = 'seeder_tracking';
    
    protected $fillable = [
        'seeder_name',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];
}
