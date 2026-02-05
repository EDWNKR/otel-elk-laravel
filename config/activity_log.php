<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activity Log Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether activity logging is enabled. When disabled,
    | no logs will be sent to Logstash.
    |
    */
    'enabled' => env('ACTIVITY_LOG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Logstash Endpoint
    |--------------------------------------------------------------------------
    |
    | The HTTP endpoint for Logstash activity input. This should be the URL
    | where your Logstash instance is listening for HTTP input.
    |
    */
    'endpoint' => env('ACTIVITY_LOG_ENDPOINT', 'http://localhost:5044'),

    /*
    |--------------------------------------------------------------------------
    | Application Identification
    |--------------------------------------------------------------------------
    |
    | These settings identify your application in the logs. This helps
    | distinguish logs from different applications in your ELK stack.
    |
    */
    'application' => env('ACTIVITY_LOG_APP_NAME', env('APP_NAME', 'laravel')),
    'environment' => env('ACTIVITY_LOG_ENVIRONMENT', env('APP_ENV', 'production')),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for sending logs to Logstash. If the request
    | takes longer than this, it will be aborted.
    |
    */
    'timeout' => env('ACTIVITY_LOG_TIMEOUT', 2),

    /*
    |--------------------------------------------------------------------------
    | Async Logging
    |--------------------------------------------------------------------------
    |
    | When enabled, logs are sent asynchronously to avoid blocking the HTTP
    | response. This is recommended for production environments.
    |
    */
    'async' => env('ACTIVITY_LOG_ASYNC', true),

    /*
    |--------------------------------------------------------------------------
    | Auto Register Middleware
    |--------------------------------------------------------------------------
    |
    | When enabled, the activity log middleware will be automatically
    | registered as global middleware. Set to false if you want to
    | manually register the middleware on specific routes.
    |
    */
    'auto_register_middleware' => env('ACTIVITY_LOG_AUTO_MIDDLEWARE', true),

    /*
    |--------------------------------------------------------------------------
    | Log Auth Events
    |--------------------------------------------------------------------------
    |
    | When enabled, authentication events (login, logout, failed, lockout,
    | registered, password reset) will be automatically logged.
    |
    */
    'log_auth_events' => env('ACTIVITY_LOG_AUTH_EVENTS', true),

    /*
    |--------------------------------------------------------------------------
    | Exclude AJAX Requests
    |--------------------------------------------------------------------------
    |
    | When enabled, AJAX/XHR requests will not be logged. This helps reduce
    | noise from DataTables, live search, and other AJAX components.
    |
    */
    'exclude_ajax' => env('ACTIVITY_LOG_EXCLUDE_AJAX', true),

    /*
    |--------------------------------------------------------------------------
    | Exclude JSON Requests
    |--------------------------------------------------------------------------
    |
    | When enabled, requests expecting JSON response will not be logged.
    |
    */
    'exclude_json_requests' => env('ACTIVITY_LOG_EXCLUDE_JSON', false),

    /*
    |--------------------------------------------------------------------------
    | Only Log Named Routes
    |--------------------------------------------------------------------------
    |
    | When enabled, only routes with names will be logged. This helps filter
    | out asset requests and focus on controller actions.
    |
    */
    'only_named_routes' => env('ACTIVITY_LOG_ONLY_NAMED_ROUTES', false),

    /*
    |--------------------------------------------------------------------------
    | Included Route Names
    |--------------------------------------------------------------------------
    |
    | If specified, only routes matching these patterns will be logged.
    | Supports wildcards. Leave empty to log all routes.
    | Example: ['orders.*', 'users.*', 'products.store']
    |
    */
    'included_routes' => [],

    /*
    |--------------------------------------------------------------------------
    | Excluded Route Names
    |--------------------------------------------------------------------------
    |
    | Routes matching these patterns will NOT be logged.
    | Supports wildcards.
    |
    */
    'excluded_routes' => [
        '*.datatable*',
        '*.datatables*',
        '*.ajax*',
        '*.search*',
        '*.autocomplete*',
        '*.suggest*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Paths
    |--------------------------------------------------------------------------
    |
    | Paths that should not be logged. You can use wildcards (*) for pattern
    | matching. Common exclusions include health checks, debug routes, etc.
    |
    */
    'excluded_paths' => [
        'health',
        'ready',
        'livez',
        'metrics',
        '_debugbar/*',
        'telescope/*',
        'horizon/*',
        'sanctum/csrf-cookie',
        // DataTables and AJAX common paths
        '*/datatable*',
        '*/datatables*',
        '*/ajax/*',
        '*/search',
        '*/autocomplete',
        '*/select2/*',
        '*/livewire/*',
        'livewire/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Methods
    |--------------------------------------------------------------------------
    |
    | HTTP methods that should not be logged. Uncomment OPTIONS if you want
    | to exclude CORS preflight requests.
    |
    */
    'excluded_methods' => [
        'OPTIONS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Request Body
    |--------------------------------------------------------------------------
    |
    | Whether to log request body. Be careful with sensitive data!
    | Sensitive fields will be automatically masked.
    |
    */
    'log_request_body' => env('ACTIVITY_LOG_REQUEST_BODY', false),

    /*
    |--------------------------------------------------------------------------
    | Sensitive Fields
    |--------------------------------------------------------------------------
    |
    | Fields to mask in request body. These fields will be replaced with
    | '***REDACTED***' in the logs.
    |
    */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'secret',
        'api_key',
        'api_secret',
        'credit_card',
        'card_number',
        'cvv',
        'cvc',
        'pin',
        'ssn',
        'authorization',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Resolver
    |--------------------------------------------------------------------------
    |
    | Customize how user information is retrieved. You can specify a custom
    | callback or class method to resolve user data.
    |
    | Supported: 'default', 'session', 'custom'
    |
    */
    'user_resolver' => env('ACTIVITY_LOG_USER_RESOLVER', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Custom User Fields
    |--------------------------------------------------------------------------
    |
    | Additional user fields to include in logs. These should be attributes
    | that exist on your User model.
    |
    */
    'user_fields' => [
        'id',
        'name',
        'email',
        'username',
    ],

    /*
    |--------------------------------------------------------------------------
    | Session User Key
    |--------------------------------------------------------------------------
    |
    | When using 'session' as user_resolver, this is the session key
    | where user data is stored.
    |
    */
    'session_user_key' => env('ACTIVITY_LOG_SESSION_USER_KEY', 'user'),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for retrying failed log submissions.
    |
    */
    'retry' => [
        'times' => 2,
        'sleep' => 100, // milliseconds
    ],
];
