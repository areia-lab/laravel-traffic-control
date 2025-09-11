# Changelog

All notable changes to this project will be documented in this file.

## [1.0.1] - 2025-09-11

### Fixed

- ✅ Resolved migration issues where multiple `traffic_logs` migrations could conflict.
- ✅ Updated migration to use **anonymous class** to prevent "Cannot declare class" errors.
- ✅ Added **duplicate migration detection** in `TrafficControlServiceProvider` to prevent publishing multiple migrations for the same table.
- ✅ Ensured safe execution on `php artisan migrate` and `php artisan migrate:fresh`.
