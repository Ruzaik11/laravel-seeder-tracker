<?php

namespace Database\Seeders;

use Ruzaik\SeederTracker\Seeders\TrackableSeeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BasicSeederExample extends TrackableSeeder
{
    protected function seedData()
    {
        // Simple user creation example
        $users = [];
        
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $users[] = $admin;

        // Create regular users
        for ($i = 1; $i <= 10; $i++) {
            $users[] = User::create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        return [
            'total_users_created' => count($users),
            'admin_created' => true,
            'regular_users_created' => count($users) - 1,
        ];
    }
}
