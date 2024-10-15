# Laravel Seeder Tracker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/ruzaik11/laravel-seeder-tracker)
[![Total Downloads](https://img.shields.io/packagist/dt/ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/ruzaik11/laravel-seeder-tracker)
[![PHP Version Require](https://img.shields.io/packagist/php-v/ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/ruzaik11/laravel-seeder-tracker)

Track your Laravel seeders execution like migrations with batch support, execution time tracking, and comprehensive status management.

## ✨ Why Use Seeder Tracker?

In large Laravel applications, managing database seeders becomes complex:
- **Duplicate executions** can corrupt data or waste time
- **No visibility** into which seeders have run
- **Performance issues** go unnoticed
- **Environment differences** cause inconsistencies

Seeder Tracker solves these problems by bringing **migration-like tracking** to your seeders!

## 🚀 Features

- 📊 **Migration-like tracking** - Know exactly which seeders have run
- ⏱️ **Performance monitoring** - Track execution time and memory usage
- 🔄 **Batch management** - Group related seeder executions
- 🛡️ **Duplicate prevention** - Prevent accidental re-runs in production
- 📈 **Rich analytics** - Detailed performance insights and reporting
- 🎯 **Environment-aware** - Smart controls based on your environment
- 💾 **Metadata storage** - Store custom data from seeder results
- 🔍 **Auto-discovery** - Automatically find and track all project seeders

## 📦 Installation

Install via Composer:

```bash
composer require ruzaik11/laravel-seeder-tracker
```

Publish configuration and migrations:

```bash
php artisan vendor:publish --provider="Ruzaik\SeederTracker\SeederTrackerServiceProvider"
php artisan migrate
```

## 🎯 Quick Start

### 1. Create a Trackable Seeder

Replace `Seeder` with `TrackableSeeder`:

```php
<?php

namespace Database\Seeders;

use Ruzaik\SeederTracker\Seeders\TrackableSeeder;
use App\Models\User;

class UsersTableSeeder extends TrackableSeeder
{
    protected function seedData()
    {
        // Your seeding logic
        $users = User::factory(100)->create();
        
        // Return metadata for tracking
        return [
            'users_created' => $users->count(),
            'admin_users' => $users->where('is_admin', true)->count(),
        ];
    }
}
```

### 2. Run Your Seeders

```bash
php artisan db:seed --class=UsersTableSeeder
# ✅ Seeder UsersTableSeeder executed successfully in 1,234ms
```

### 3. Check Execution Status

```bash
php artisan seeder:status
```

```
📊 Seeder Execution Status

+------------------+-------------+------------------+-------+
| Seeder           | Status      | Executed At      | Batch |
+------------------+-------------+------------------+-------+
| UsersTableSeeder | ✅ Executed | Dec 21, 2024 14:30 | 1    |
| ProductSeeder    | ⏳ Pending   | Not executed     | N/A   |
+------------------+-------------+------------------+-------+

Summary: 1/2 seeders executed
```

## 📋 Available Commands

### Status Management
```bash
# Basic status
php artisan seeder:status

# Detailed view with performance metrics
php artisan seeder:status --detailed

# Reset specific seeder
php artisan seeder:status --reset=UsersTableSeeder

# Reset all tracking (with confirmation)
php artisan seeder:status --reset-all
```

### Performance Analytics
```bash
# Show performance insights
php artisan seeder:performance

# Limit results
php artisan seeder:performance --limit=5
```

## 🔧 Advanced Usage

### Environment-Aware Seeding

```php
use Ruzaik\SeederTracker\Traits\SeederHelper;

class ProductionDataSeeder extends TrackableSeeder
{
    use SeederHelper;
    
    protected function seedData()
    {
        // Only run in specific environments
        return $this->runInEnvironments(['production', 'staging'], function () {
            // Production-specific seeding logic
            return ['production_data_created' => true];
        });
    }
}
```

### Performance Optimization

```php
protected function seedData()
{
    $startCount = $this->getTableCount('products');
    
    // Batch insert for better performance
    Product::insert($this->generateProductData(1000));
    
    return [
        'products_created' => $this->getTableCount('products') - $startCount,
        'batch_size' => 1000,
        'optimization' => 'batch_insert_used',
    ];
}
```

### Transaction Safety

```php
protected function seedData()
{
    return DB::transaction(function () {
        // All seeding logic in transaction
        $categories = Category::createMany($this->categoryData());
        $products = Product::createMany($this->productData());
        
        return [
            'categories_created' => count($categories),
            'products_created' => count($products),
            'transaction_safe' => true,
        ];
    });
}
```

## ⚙️ Configuration

Customize behavior in `config/seeder-tracker.php`:

```php
return [
    // Database table for tracking
    'table' => 'seeder_tracking',
    
    // Enable automatic tracking
    'auto_track' => true,
    
    // Prevent duplicate executions
    'prevent_duplicates' => env('SEEDER_PREVENT_DUPLICATES', true),
    
    // Strict environments (always prevent duplicates)
    'strict_environments' => ['production'],
];
```

## 📈 Performance Monitoring

Track seeder performance automatically:

```bash
php artisan seeder:performance
```

```
📊 Performance Summary

Total executions: 15
Average execution time: 1,247ms
Total time spent: 18.7s
Average memory usage: 12.3MB

🐌 Slowest Seeders
+------------------+----------------+-------------+------------------+
| Seeder           | Execution Time | Memory Used | Executed At      |
+------------------+----------------+-------------+------------------+
| ProductSeeder    | 3,456ms        | 25.4MB      | Dec 21, 14:30    |
| UsersTableSeeder | 2,123ms        | 15.7MB      | Dec 21, 14:25    |
+------------------+----------------+-------------+------------------+

🏃 Fastest Seeders
+------------------+----------------+-------------+------------------+
| Seeder           | Execution Time | Memory Used | Executed At      |
+------------------+----------------+-------------+------------------+
| RoleSeeder       | 45ms           | 2.1MB       | Dec 21, 14:20    |
| SettingSeeder    | 67ms           | 3.2MB       | Dec 21, 14:22    |
+------------------+----------------+-------------+------------------+
```

## 🧪 Testing

The package includes comprehensive tests:

```bash
# Run tests
composer test

# Run with coverage
composer test-coverage
```

## 📚 Examples

Check the `examples/` directory for:
- Basic seeder implementation
- Advanced environment-aware seeding
- Performance optimization techniques
- Transaction handling patterns

## 🔧 Troubleshooting

See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for common issues and solutions.

## 📈 Development Journey

This package was developed over 3+ months with focus on:

- **October 2024**: Foundation and core architecture
- **November 2024**: Tracking logic and performance monitoring  
- **December 2024**: Enhanced commands and comprehensive documentation
- **January 2025**: Community feedback and optimization

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Submit a pull request

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

## 👨‍💻 Credits

- **[Ruzaik Nazeer](https://github.com/Ruzaik11)** - Creator and maintainer
- **Laravel Community** - Inspiration and feedback

---

<p align="center">
  <strong>Built with ❤️ for the Laravel community</strong><br>
  <a href="https://ruzaik.dev">Website</a> •
  <a href="mailto:ruzaiknazeer@gmail.com">Email</a> •
  <a href="https://packagist.org/packages/ruzaik11/laravel-seeder-tracker">Packagist</a>
</p>
