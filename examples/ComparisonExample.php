<?php

/**
 * Comparison: TracksExecution Trait vs TrackableSeeder
 * 
 * This file shows the difference between the two approaches
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ruzaik\SeederTracker\Seeders\TrackableSeeder;
use Ruzaik\SeederTracker\Traits\TracksExecution;

// ========================================
// APPROACH 1: Using TracksExecution Trait
// ========================================

/**
 * Trait approach - for existing seeders or when you prefer composition
 */
class UsersSeederWithTrait extends Seeder
{
    use TracksExecution;

    protected function executeSeederLogic()
    {
        // Your existing seeder logic
        \App\Models\User::factory(10)->create();

        return ['users_created' => 10];
    }
}

/**
 * Alternative trait usage - using seedData method
 */
class ProductsSeederWithTrait extends Seeder
{
    use TracksExecution;

    protected function seedData()
    {
        // Works exactly like TrackableSeeder
        \App\Models\Product::factory(5)->create();

        return ['products_created' => 5];
    }
}

// ========================================
// APPROACH 2: Extending TrackableSeeder
// ========================================

/**
 * Traditional inheritance approach
 */
class UsersSeederWithInheritance extends TrackableSeeder
{
    protected function seedData()
    {
        \App\Models\User::factory(10)->create();

        return ['users_created' => 10];
    }
}

// ========================================
// COMPARISON SUMMARY
// ========================================

/*
TracksExecution Trait:
✅ No inheritance required
✅ Works with existing seeders
✅ Two method options: executeSeederLogic() or seedData()
✅ Optional return data
✅ Same tracking features

TrackableSeeder:
✅ Clean inheritance
✅ Required seedData() method  
✅ Implements TrackableSeederInterface
✅ More structured approach

Both provide:
- Performance tracking
- Duplicate prevention
- Execution metadata
- Batch management
- Status commands compatibility
*/
