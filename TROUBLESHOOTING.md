# Troubleshooting Guide

## Common Issues and Solutions

### Installation Issues

**Q: Package not found when installing**
```bash
composer require ruzaik11/laravel-seeder-tracker
```

**A:** Make sure you're using the correct package name and have proper internet connection.

**Q: Service provider not auto-discovered**

**A:** Add manually to `config/app.php`:
```php
'providers' => [
    // ...
    Ruzaik11\SeederTracker\SeederTrackerServiceProvider::class,
],
```

### Migration Issues

**Q: Migration fails to run**

**A:** Ensure you've published the migrations first:
```bash
php artisan vendor:publish --provider="Ruzaik11\SeederTracker\SeederTrackerServiceProvider"
php artisan migrate
```

### Usage Issues

**Q: Seeder runs multiple times despite tracking**

**A:** Check your configuration:
```php
// config/seeder-tracker.php
'prevent_duplicates' => true,
```

**Q: Performance metrics not showing**

**A:** Ensure your seeder extends `TrackableSeeder` and returns metadata:
```php
protected function seedData()
{
    // Your seeding logic
    return ['records_created' => 100];
}
```

### Environment Issues

**Q: Seeder behaves differently in production**

**A:** Check environment-specific configuration:
```php
'strict_environments' => ['production'],
```

### Command Issues

**Q: seeder:status command not available**

**A:** Clear your application cache:
```bash
php artisan config:clear
php artisan cache:clear
```
