<?php

return [
    'table' => 'seeder_tracking',
    'auto_track' => true,
    'prevent_duplicates' => env('SEEDER_PREVENT_DUPLICATES', true),
    'strict_enviroments' => ['production'],
];
