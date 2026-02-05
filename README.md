# OpenTelemetry ELK Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/edwinekr/otel-elk-laravel.svg?style=flat-square)](https://packagist.org/packages/edwinekr/otel-elk-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/edwinekr/otel-elk-laravel.svg?style=flat-square)](https://packagist.org/packages/edwinekr/otel-elk-laravel)

OpenTelemetry activity logging integration with ELK Stack (Elasticsearch, Logstash, Kibana) for Laravel applications.

## Architecture

```
Laravel App → OpenTelemetry SDK → Logstash HTTP (5044) → Elasticsearch → Kibana (Map View)
```

## Features

- 🚀 **Automatic HTTP Request Logging** - All requests are logged with response time, status codes, and user info
- 🔐 **Authentication Event Logging** - Login, logout, failed attempts, lockouts, and password resets
- 📊 **Model Activity Logging** - Track create, update, and delete operations on Eloquent models
- 🎯 **Custom Activity Logging** - Log any custom activity with metadata
- ⚡ **Async Logging** - Non-blocking log submission for better performance
- 🔒 **Sensitive Data Masking** - Automatically redact passwords, tokens, and other sensitive fields
- 🌍 **GeoIP Ready** - Works with Logstash GeoIP filter for location visualization in Kibana

## Requirements

- PHP 8.1+
- Laravel 10.x, 11.x, or 12.x
- Guzzle HTTP Client

## Installation

Install the package via Composer:

```bash
composer require edwinekr/otel-elk-laravel
```

The package will auto-register its service provider.

### Publish Configuration

```bash
php artisan vendor:publish --tag=otel-elk-config
```

This will create `config/activity_log.php` in your application.

## Configuration

Add the following to your `.env` file:

```env
# ===========================================
# Activity Log / OpenTelemetry Configuration
# ===========================================

# Enable/disable activity logging (default: true)
ACTIVITY_LOG_ENABLED=true

# Logstash HTTP endpoint for receiving logs
ACTIVITY_LOG_ENDPOINT=http://localhost:5044

# Application name for log identification
ACTIVITY_LOG_APP_NAME=my-laravel-app

# Environment identifier (development, staging, production)
ACTIVITY_LOG_ENVIRONMENT=production

# Request timeout in seconds for sending logs
ACTIVITY_LOG_TIMEOUT=2

# Send logs asynchronously (recommended for performance)
ACTIVITY_LOG_ASYNC=true

# Log request body (⚠️ be careful with sensitive data)
ACTIVITY_LOG_REQUEST_BODY=false

# Auto-register middleware globally (default: true)
ACTIVITY_LOG_AUTO_MIDDLEWARE=true

# Log authentication events (default: true)
ACTIVITY_LOG_AUTH_EVENTS=true

# User resolver: 'default', 'session', or 'custom'
ACTIVITY_LOG_USER_RESOLVER=default

# Session key for user data (when using session resolver)
ACTIVITY_LOG_SESSION_USER_KEY=user
```

## Usage

### Automatic HTTP Request Logging

All HTTP requests are automatically logged when the middleware is enabled. No additional code required!

### Manual Activity Logging

Use the `ActivityLog` facade or inject `ActivityLogService`:

```php
use Edwinekr\OtelElkLaravel\Facades\ActivityLog;

// Log a custom activity
ActivityLog::log(
    action: 'order.checkout',
    entity: 'Order',
    entityId: $order->id,
    metadata: [
        'total' => $order->total,
        'items_count' => $order->items->count(),
        'payment_method' => $order->payment_method,
    ]
);
```

### Using Dependency Injection

```php
use Edwinekr\OtelElkLaravel\Services\ActivityLogService;

class OrderController extends Controller
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function checkout(Request $request)
    {
        $order = Order::create([...]);

        $this->activityLog->log(
            action: 'order.checkout',
            entity: 'Order',
            entityId: $order->id,
            metadata: [
                'total' => $order->total,
                'items_count' => $order->items->count(),
            ]
        );

        return response()->json(['order' => $order]);
    }
}
```

### Model Activity Logging

Use the `LogsActivity` trait on your Eloquent models:

```php
use Edwinekr\OtelElkLaravel\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

    // Model will automatically log created, updated, deleted events
}
```

You can also log custom activities on a model:

```php
$product->logActivity('viewed', ['viewer_ip' => request()->ip()]);
```

### Authentication Events

Authentication events are automatically logged when enabled. These events are tracked:

- `auth.login` - User logged in
- `auth.logout` - User logged out
- `auth.failed` - Login attempt failed
- `auth.lockout` - User was locked out
- `auth.registered` - New user registered
- `auth.password_reset` - Password was reset

### Using Session-Based User Resolver

If your application uses session-based authentication instead of Laravel's built-in auth:

```env
ACTIVITY_LOG_USER_RESOLVER=session
ACTIVITY_LOG_SESSION_USER_KEY=user
```

Make sure your session contains user data:

```php
// Store user in session
Session::put('user', [
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
]);
```

### Manual Middleware Registration

If you prefer to register the middleware manually, disable auto-registration:

```env
ACTIVITY_LOG_AUTO_MIDDLEWARE=false
```

Then register it in your routes or kernel:

```php
// In routes/api.php
Route::middleware(['activity-log'])->group(function () {
    // Your routes
});
```

Or for Laravel 11+ in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\Edwinekr\OtelElkLaravel\Middleware\ActivityLogMiddleware::class);
})
```

## Excluding Paths and Methods

Configure excluded paths in `config/activity_log.php`:

```php
'excluded_paths' => [
    'health',
    'ready',
    'livez',
    'metrics',
    '_debugbar/*',
    'telescope/*',
    'horizon/*',
    'sanctum/csrf-cookie',
    'api/internal/*',
],

'excluded_methods' => [
    'OPTIONS',
],
```

## Sensitive Fields

Sensitive fields are automatically masked in request body logs:

```php
'sensitive_fields' => [
    'password',
    'password_confirmation',
    'token',
    'secret',
    'api_key',
    'credit_card',
    'cvv',
],
```

## Logstash Configuration

Example Logstash configuration for receiving logs:

```ruby
input {
    http {
        port => 5044
        codec => json
    }
}

filter {
    # Add GeoIP data based on client IP
    if [ip] {
        geoip {
            source => "ip"
            target => "geoip"
        }
    }

    # Parse timestamp
    date {
        match => ["timestamp", "ISO8601"]
        target => "@timestamp"
    }
}

output {
    elasticsearch {
        hosts => ["elasticsearch:9200"]
        index => "activity-logs-%{+YYYY.MM.dd}"
    }
}
```

## Kibana Visualization

After logs are ingested, you can create visualizations in Kibana:

1. Go to **Kibana → Maps**
2. Click **Add layer → Documents**
3. Select your index pattern `activity-logs-*`
4. Choose `geoip.location` as the geospatial field
5. Configure tooltips to show: `ip`, `user_email`, `action`, `path`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email security@example.com instead of using the issue tracker.

## Credits

- [Edwin KR](https://github.com/EDWNKR)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
