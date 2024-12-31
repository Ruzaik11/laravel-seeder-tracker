# Changelog

All notable changes to `laravel-seeder-tracker` will be documented in this file.

## [1.0.0] - 2024-12-24

### Added
- Initial release of Laravel Seeder Tracker
- Track seeder execution like Laravel migrations
- Batch support for organized seeding campaigns
- Performance monitoring with execution time tracking
- Metadata storage for custom seeder information
- Artisan commands for status management and reset functionality
- Environment-aware duplicate prevention
- Configurable behavior via config file
- Helper traits for common seeding scenarios
- Comprehensive documentation and examples

### Features
- `TrackableSeeder` base class for automatic tracking
- `seeder:status` command with detailed reporting
- Reset individual or all seeder tracking records
- Auto-discovery of project seeders
- Support for Laravel 9, 10, and 11
- PHP 8.1+ compatibility
