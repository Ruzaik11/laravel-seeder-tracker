<?php

/**
 * Example Usage of seeder:mark Command
 * 
 * This demonstrates different scenarios where you'd use the seeder:mark command
 */

/*
========================
SCENARIO 1: Migration from untracked state
========================

You have an existing Laravel app with seeders that have already been run manually.
Now you want to start using seeder tracking without re-running existing data.

# First, check what seeders exist
php artisan seeder:mark --list

Output:
📋 Available Seeders:

+------------------+-----------+----------------------------------------+
| Seeder           | Status    | File Path                              |
+------------------+-----------+----------------------------------------+
| UsersSeeder      | ⏳ Pending | /app/database/seeders/UsersSeeder.php  |
| ProductsSeeder   | ⏳ Pending | /app/database/seeders/ProductsSeeder.php |
| CategoriesSeeder | ⏳ Pending | /app/database/seeders/CategoriesSeeder.php |
+------------------+-----------+----------------------------------------+

Total: 3 seeders, 3 pending

# Mark all as executed since they've already been run
php artisan seeder:mark --all

Output:
Found 3 pending seeders:
  - UsersSeeder
  - ProductsSeeder  
  - CategoriesSeeder

Mark all these seeders as executed? (yes/no) [no]:
> yes

✅ Marked 3 seeders as executed in batch 1

========================
SCENARIO 2: Mark specific seeder
========================

You manually created some test data and want to mark just one seeder as executed.

php artisan seeder:mark --seeder=UsersSeeder

Output:
✅ Marked UsersSeeder as executed in batch 2

========================
SCENARIO 3: Production deployment
========================

In production, you want to mark seeders without confirmation prompts.

php artisan seeder:mark --all --force

Output:
✅ Marked 2 seeders as executed in batch 3

========================
SCENARIO 4: Custom batch tracking
========================

You want to group certain seeders together with a specific batch number.

php artisan seeder:mark --seeder=UsersSeeder --batch=100
php artisan seeder:mark --seeder=RolesSeeder --batch=100

# Now check status
php artisan seeder:status

Output:
📊 Seeder Execution Status

+------------------+-------------+------------------+-------+
| Seeder           | Status      | Executed At      | Batch |
+------------------+-------------+------------------+-------+
| UsersSeeder      | ✅ Executed | Dec 21, 2024 15:30 | 100  |
| RolesSeeder      | ✅ Executed | Dec 21, 2024 15:31 | 100  |
| ProductsSeeder   | ⏳ Pending   | Not executed     | N/A   |
+------------------+-------------+------------------+-------+

========================
SCENARIO 5: Checking what was marked manually
========================

Seeders marked with seeder:mark have special metadata to distinguish them
from seeders that were actually executed.

The metadata includes:
- marked_manually: true
- marked_by_command: true
- execution_time_ms: 0
- memory_used_mb: 0

You can see this in the detailed status:
php artisan seeder:status --detailed

========================
SCENARIO 6: Re-marking already executed seeders
========================

If you try to mark an already executed seeder:

php artisan seeder:mark --seeder=UsersSeeder

Output:
Seeder UsersSeeder is already marked as executed.
Mark it again anyway? (yes/no) [no]:

# Or force it:
php artisan seeder:mark --seeder=UsersSeeder --force

Output:
✅ Marked UsersSeeder as executed in batch 4

========================
SCENARIO 7: Partial name matching
========================

You can use partial seeder names for convenience:

php artisan seeder:mark --seeder=User

# Works if only one seeder matches "User"

php artisan seeder:mark --seeder=Table

Output:
Multiple seeders match 'Table':
  - UsersTableSeeder
  - ProductsTableSeeder
Please be more specific.

========================
WORKFLOW EXAMPLE
========================

Typical workflow when adopting seeder tracking:

1. Install package
   composer require ruzaik11/laravel-seeder-tracker

2. Publish and migrate
   php artisan vendor:publish --provider="Ruzaik\SeederTracker\SeederTrackerServiceProvider"
   php artisan migrate

3. Check existing seeders
   php artisan seeder:mark --list

4. Mark already-run seeders
   php artisan seeder:mark --all

5. Update future seeders to use TracksExecution trait or extend TrackableSeeder

6. Normal seeding going forward
   php artisan db:seed --class=NewSeeder

7. Monitor with status and performance commands
   php artisan seeder:status
   php artisan seeder:performance

*/
