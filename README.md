# Laravel Seeder Tracker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/Ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/Ruzaik11/laravel-seeder-tracker)
[![Total Downloads](https://img.shields.io/packagist/dt/Ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/Ruzaik11/laravel-seeder-tracker)
[![PHP Version Require](https://img.shields.io/packagist/php-v/Ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/Ruzaik11/laravel-seeder-tracker)

Track your Laravel seeders execution like migrations with batch support, execution time tracking, and comprehensive status management.

## ✨ Features

- 🚀 **Migration-like tracking** - Track seeder execution just like Laravel migrations
- ⏱️ **Performance monitoring** - Monitor execution time and performance metrics  
- 🔄 **Batch management** - Group related seeder executions for organized campaigns
- 🛡️ **Duplicate prevention** - Prevent accidental re-runs in production environments
- 📊 **Rich reporting** - Detailed status reporting via Artisan commands
- 🎯 **Environment-aware** - Smart seeding controls based on environment
- 💾 **Metadata storage** - Store custom metadata from seeder results
- 🔍 **Auto-discovery** - Automatically discover and track all project seeders

## 📦 Installation

Install the package via Composer:

```bash
composer require Ruzaik11/laravel-seeder-tracker
```

Publish the migration and configuration:

```bash
php artisan vendor:publish --provider="Ruzaik11\SeederTracker\SeederTrackerServiceProvider"
```

Run the migration:

```bash
php artisan migrate
```

## 🚀 Quick Start

### 1. Create a Trackable Seeder

Extend the `TrackableSeeder` class instead of Laravel's base `Seeder`:

```php
<?php

namespace Database\Seeders;

use Ruzaik11\SeederTracker\Seeders\TrackableSeeder;
use App\Models\User;

class UsersTableSeeder extends TrackableSeeder
{
    protected function seedData()
    {
        $users = User::factory(50)->create();
        
        // Return metadata to track with execution
        return [
            'users_created' => $users->count(),
            'admin_users' => $users->where('is_admin', true)->count()
        ];
    }
}
```

### 2. Run Your Seeders

```bash
php artisan db:seed --class=UsersTableSeeder
```

### 3. Check Status

```bash
php artisan seeder:status
```

## 📋 Available Commands

### View Seeder Status
```bash
# Basic status view
php artisan seeder:status

# Include performance metrics
php artisan seeder:status --show-performance
```

### Reset Tracking
```bash
# Reset specific seeder
php artisan seeder:status --reset=UsersTableSeeder

# Reset all seeder tracking (with confirmation)
php artisan seeder:status --reset-all
```

## 🔧 Advanced Usage

### Environment-Aware Seeding

Use the `SeederHelper` trait for environment-specific logic:

```php
<?php

namespace Database\Seeders;

use Ruzaik11\SeederTracker\Seeders\TrackableSeeder;
use Ruzaik11\SeederTracker\Traits\SeederHelper;

class ProductionDataSeeder extends TrackableSeeder
{
    use SeederHelper;
    
    protected function seedData()
    {
        // Only run in specific environments
        return $this->runInEnvironments(['production', 'staging'], function () {
            // Your production seeding logic
            return ['status' => 'production_data_seeded'];
        });
    }
}
```

### Custom Metadata Tracking

Track detailed information about your seeding process:

```php
protected function seedData()
{
    $startTime = microtime(true);
    $initialCount = $this->getTableCount('products');
    
    // Your seeding logic
    Product::factory(100)->create();
    
    return [
        'products_created' => $this->getTableCount('products') - $initialCount,
        'categories_used' => Category::count(),
        'custom_execution_time' => microtime(true) - $startTime,
        'memory_peak' => memory_get_peak_usage(true),
    ];
}
```

## ⚙️ Configuration

Publish and customize the configuration file:

```php
<?php
// config/seeder-tracker.php

return [
    // Database table name for tracking
    'table' => 'seeder_tracking',
    
    // Enable automatic tracking
    'auto_track' => true,
    
    // Prevent duplicate executions
    'prevent_duplicates' => env('SEEDER_PREVENT_DUPLICATES', true),
    
    // Environments where duplicates are strictly prevented
    'strict_environments' => ['production'],
];
```

## 🧪 Testing

The package includes comprehensive tests. Run them with:

```bash
composer test
```

## 📈 Development Journey

This package was carefully developed over 3 months with attention to:

- **October 2024**: Project foundation, database structure, core concepts
- **November 2024**: Implementation of tracking logic, basic commands, batch support  
- **December 2024**: Advanced features, performance monitoring, comprehensive documentation
- **January 2025**: Bug fixes, UX improvements, performance metrics

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 👨‍💻 Credits

- **[Ruzaik Nazeer](https://github.com/ruzaiknazeer)** - Full Stack Developer with 4+ years experience
- [All Contributors](../../contributors)

---

<p align="center">Built with ❤️ for the Laravel community</p>
<p align="center">
  <a href="https://ruzaik.dev">Website</a> •
  <a href="mailto:ruzaiknazeer@gmail.com">Email</a> •
  <a href="https://packagist.org/packages/Ruzaik11/laravel-seeder-tracker">Packagist</a>
</p>
