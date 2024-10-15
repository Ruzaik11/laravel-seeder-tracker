<?php

namespace Database\Seeders;

use Ruzaik\SeederTracker\Seeders\TrackableSeeder;
use Ruzaik\SeederTracker\Traits\SeederHelper;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdvancedSeederExample extends TrackableSeeder
{
    use SeederHelper;

    protected function seedData()
    {
        // Environment-aware seeding
        return $this->runInEnvironments(['local', 'staging'], function () {
            
            DB::transaction(function () {
                // Create categories first
                $categories = [
                    'Electronics',
                    'Clothing',
                    'Books',
                    'Home & Garden',
                    'Sports'
                ];

                foreach ($categories as $categoryName) {
                    Category::firstOrCreate(['name' => $categoryName]);
                }

                // Create products for each category
                $productsCreated = 0;
                foreach (Category::all() as $category) {
                    for ($i = 1; $i <= 5; $i++) {
                        Product::create([
                            'name' => "{$category->name} Product {$i}",
                            'category_id' => $category->id,
                            'price' => rand(10, 500),
                            'description' => "Sample product for {$category->name}",
                        ]);
                        $productsCreated++;
                    }
                }

                return [
                    'categories_created' => count($categories),
                    'products_created' => $productsCreated,
                    'environment' => app()->environment(),
                    'transaction_used' => true,
                ];
            });
        });
    }
}
