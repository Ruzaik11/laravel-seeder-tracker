# Laravel Seeder Tracker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/Ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/Ruzaik11/laravel-seeder-tracker)
[![Total Downloads](https://img.shields.io/packagist/dt/Ruzaik11/laravel-seeder-tracker.svg?style=flat-square)](https://packagist.org/packages/Ruzaik11/laravel-seeder-tracker)

Track your Laravel seeders execution like migrations with batch support, execution time tracking, and comprehensive status management.

## Features

- 🚀 Track seeder execution like Laravel migrations
- ⏱️ Monitor execution time and performance metrics
- 🔄 Batch management for organized seeding campaigns
- 🛡️ Prevent duplicate executions in production environments
- 📊 Detailed status reporting via Artisan commands
- 🎯 Environment-aware seeding controls
- 💾 Store custom metadata from seeder results
- 🔍 Discover and track all project seeders automatically

## Installation

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

## Usage

### Creating Trackable Seeders

Extend the `TrackableSeeder` class instead of Laravel's base `Seeder`:

```php
use Ruzaik11\SeederTracker\Seeders\TrackableSeeder;

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

### Artisan Commands

**View seeder status:**
```bash
php artisan seeder:status
```

**Reset specific seeder:**
```bash
php artisan seeder:status --reset=UsersTableSeeder
```

**Reset all seeder tracking:**
```bash
php artisan seeder:status --reset-all
```

### Configuration

The package provides several configuration options in `config/seeder-tracker.php`:

```php
return [
    'table' => 'seeder_tracking',
    'auto_track' => true,
    'prevent_duplicates' => env('SEEDER_PREVENT_DUPLICATES', true),
    'strict_environments' => ['production'],
];
```

## Development

This package was developed over several months with careful attention to:

- **Performance tracking** - Monitor seeder execution times
- **Batch management** - Group related seeder executions
- **Environment safety** - Prevent accidental re-runs in production
- **Developer experience** - Clear status reporting and easy management

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Ruzaik Nazeer](https://github.com/ruzaiknazeer)
- [All Contributors](../../contributors)

---

Built with ❤️ for the Laravel community
