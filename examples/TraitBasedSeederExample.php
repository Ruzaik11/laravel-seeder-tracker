<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ruzaik\SeederTracker\Traits\TracksExecution;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

/**
 * Example 1: Simple seeder using executeSeederLogic() method
 */
class UsersSeederWithTrait extends Seeder
{
    use TracksExecution;

    protected function executeSeederLogic()
    {
        // Your existing seeder logic - no changes needed to your original code
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $regularUsers = User::factory(50)->create();

        // Optionally return metadata for tracking
        return [
            'admin_created' => true,
            'regular_users_count' => $regularUsers->count(),
            'total_users' => User::count(),
        ];
    }
}

/**
 * Example 2: Using seedData() method (compatible with TrackableSeeder)
 */
class ProductsSeederWithTrait extends Seeder
{
    use TracksExecution;

    protected function seedData()
    {
        // This works just like TrackableSeeder
        $products = collect();

        for ($i = 1; $i <= 25; $i++) {
            $products->push(Product::create([
                'name' => "Product {$i}",
                'price' => rand(10, 1000),
                'description' => "Description for product {$i}",
            ]));
        }

        return [
            'products_created' => $products->count(),
            'price_range' => [
                'min' => $products->min('price'),
                'max' => $products->max('price'),
            ],
        ];
    }
}

/**
 * Example 3: Legacy seeder with minimal changes
 */
class LegacySeederWithTrait extends Seeder
{
    use TracksExecution;

    protected function executeSeederLogic()
    {
        // Your existing legacy seeder code
        $this->createCategories();
        $this->createUsers();
        $this->createProducts();

        // No return data needed - just performance tracking
    }

    private function createCategories()
    {
        // Your existing code...
    }

    private function createUsers()
    {
        // Your existing code...
    }

    private function createProducts()
    {
        // Your existing code...
    }
}

/**
 * Example 4: Advanced usage with custom metadata
 */
class AdvancedSeederWithTrait extends Seeder
{
    use TracksExecution;

    protected function executeSeederLogic()
    {
        $startUserCount = User::count();

        // Use trackWithMetadata for granular tracking
        return $this->trackWithMetadata(function () {
            $users = User::factory(100)->create();
            $products = Product::factory(200)->create();

            return [
                'users_created' => $users->count(),
                'products_created' => $products->count(),
            ];
        }, [
            'strategy' => 'factory_batch',
            'environment' => app()->environment(),
            'initial_user_count' => $startUserCount,
        ]);
    }
}
