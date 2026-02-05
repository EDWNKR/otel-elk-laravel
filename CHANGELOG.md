# Changelog

All notable changes to `otel-elk-laravel` will be documented in this file.

## [1.0.0] - 2026-02-05

### Added
- Initial release
- HTTP request activity logging with middleware
- Authentication event logging (login, logout, failed, lockout, registered, password reset)
- Model activity logging with `LogsActivity` trait
- Custom activity logging via `ActivityLogService`
- `ActivityLog` facade for easy access
- Async logging support for better performance
- Sensitive field masking
- Configurable excluded paths and methods
- Session-based user resolver option
- Retry configuration for failed log submissions
- Full Laravel 10 and 11 support
