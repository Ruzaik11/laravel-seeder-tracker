<?php

namespace Database\Seeders;

use Ruzaik\SeederTracker\Seeders\TrackableSeeder;
use Ruzaik\SeederTracker\Traits\SeederHelper;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ExampleSeeder extends TrackableSeeder
{
    use SeederHelper;

    protected function seedData()
    {
        // Example: Environment-aware seeding
        return $this->runInEnvironments(['local', 'staging'], function () {
            $startCount = $this->getTableCount('users');
            
            // Create admin user
            $admin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Create test users
            $users = User::factory(10)->create();

            // Assign roles if they exist
            if (class_exists(Role::class)) {
                $adminRole = Role::firstOrCreate(['name' => 'admin']);
                $admin->assignRole($adminRole);
            }

            $endCount = $this->getTableCount('users');
            $created = $endCount - $startCount;

            return [
                'users_created' => $created,
                'admin_created' => $admin->wasRecentlyCreated,
                'environment' => app()->environment(),
                'roles_assigned' => class_exists(Role::class),
            ];
        });
    }
}
