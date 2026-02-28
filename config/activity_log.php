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
    | Exclude Static Assets
    |--------------------------------------------------------------------------
    |
    | When enabled, static asset requests (CSS, JS, images, fonts) will not
    | be logged. This is highly recommended to reduce log noise.
    |
    */
    'exclude_static_assets' => env('ACTIVITY_LOG_EXCLUDE_ASSETS', true),

    /*
    |--------------------------------------------------------------------------
    | Static Asset Extensions
    |--------------------------------------------------------------------------
    |
    | File extensions considered as static assets.
    |
    */
    'static_asset_extensions' => [
        'css', 'js', 'map',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'avif',
        'woff', 'woff2', 'ttf', 'eot', 'otf',
        'mp3', 'mp4', 'webm', 'ogg', 'wav',
        'pdf', 'doc', 'docx', 'xls', 'xlsx',
        'zip', 'rar', 'tar', 'gz',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude Redirects
    |--------------------------------------------------------------------------
    |
    | When enabled, redirect responses (3xx status codes) will not be logged.
    |
    */
    'exclude_redirects' => env('ACTIVITY_LOG_EXCLUDE_REDIRECTS', true),

    /*
    |--------------------------------------------------------------------------
    | Only Log Controller Actions
    |--------------------------------------------------------------------------
    |
    | When enabled, only requests handled by controller methods will be logged.
    | Closure routes and static file routes will be excluded.
    |
    */
    'only_controller_actions' => env('ACTIVITY_LOG_ONLY_CONTROLLERS', true),

    /*
    |--------------------------------------------------------------------------
    | Only Log HTML Requests
    |--------------------------------------------------------------------------
    |
    | When enabled, only requests with Accept header containing text/html
    | will be logged. This excludes CSS, JS, and other asset preloads.
    |
    */
    'only_html_requests' => env('ACTIVITY_LOG_ONLY_HTML', true),

    /*
    |--------------------------------------------------------------------------
    | Only Log Named Routes
    |--------------------------------------------------------------------------
    |
    | When enabled, only routes with names will be logged. This helps filter
    | out asset requests and focus on controller actions.
    |
    */
    'only_named_routes' => env('ACTIVITY_LOG_ONLY_NAMED_ROUTES', true),

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

    /*
    |--------------------------------------------------------------------------
    | IP Resolution
    |--------------------------------------------------------------------------
    |
    | Configure how client IP addresses are resolved.
    |
    */
    'ip' => [
        /*
        | Trusted proxy headers to check for real IP (in order of priority)
        | Common headers: X-Forwarded-For, X-Real-IP, CF-Connecting-IP (Cloudflare)
        */
        'trusted_headers' => [
            'CF-Connecting-IP',     // Cloudflare
            'True-Client-IP',       // Akamai, Cloudflare Enterprise
            'X-Real-IP',            // Nginx proxy
            'X-Forwarded-For',      // Standard proxy header
            'X-Client-IP',          // Apache
            'X-Cluster-Client-IP',  // Load balancers
        ],

        /*
        | Fallback IP for local/development environments
        | This IP will be used when the detected IP is localhost (::1, 127.0.0.1)
        | Default: Banten, Indonesia IP
        */
        'local_fallback' => env('ACTIVITY_LOG_LOCAL_IP', '103.28.12.1'),

        /*
        | Use fallback IP in local environment
        | Set to false to log actual localhost IP
        */
        'use_local_fallback' => env('ACTIVITY_LOG_USE_LOCAL_IP', true),

        /*
        | Private/local IP ranges that should use fallback
        */
        'local_ranges' => [
            '127.0.0.1',
            '::1',
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            'fc00::/7',
            'fe80::/10',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Elastic APM RUM (Real User Monitoring)
    |--------------------------------------------------------------------------
    |
    | Configuration for Elastic APM Real User Monitoring (RUM). This enables
    | browser-side performance monitoring and error tracking.
    |
    */
    'elastic_apm_rum' => [
        /*
        | Enable/Disable RUM
        | When disabled, the RUM script will not be injected into pages.
        */
        'enabled' => env('ELASTIC_APM_RUM_ENABLED', false),

        /*
        | RUM Service Name
        | The name of your service as it will appear in Elastic APM.
        */
        'service_name' => env('ELASTIC_APM_RUM_SERVICE_NAME', env('APP_NAME', 'laravel')),

        /*
        | RUM Server URL
        | The URL of your Elastic APM server's RUM endpoint.
        */
        'server_url' => env('ELASTIC_APM_RUM_URL', ''),

        /*
        | RUM Secret Token (optional)
        | Secret token for authenticating with the APM server.
        | Note: RUM typically uses API keys, but token is supported.
        */
        'secret_token' => env('ELASTIC_APM_RUM_TOKEN', ''),

        /*
        | RUM API Key (optional)
        | API key for authenticating with the APM server.
        | Preferred over secret token for RUM.
        */
        'api_key' => env('ELASTIC_APM_RUM_API_KEY', ''),

        /*
        | Service Version
        | Version of your application for tracking deployments.
        */
        'service_version' => env('ELASTIC_APM_RUM_SERVICE_VERSION', env('APP_VERSION', '1.0.0')),

        /*
        | Environment
        | The deployment environment (production, staging, development).
        */
        'environment' => env('ELASTIC_APM_RUM_ENVIRONMENT', env('APP_ENV', 'production')),

        /*
        | Transaction Sample Rate
        | Percentage of transactions to capture (0.0 to 1.0).
        | 1.0 = 100% of transactions, 0.5 = 50%, etc.
        */
        'transaction_sample_rate' => env('ELASTIC_APM_RUM_SAMPLE_RATE', 1.0),

        /*
        | Page Load Transaction Name
        | Name pattern for page load transactions.
        */
        'page_load_transaction_name' => env('ELASTIC_APM_RUM_PAGE_LOAD_NAME', 'Page Load'),

        /*
        | Enable Page Load Span ID
        | Whether to include span IDs in page load transactions.
        */
        'page_load_span_id' => env('ELASTIC_APM_RUM_PAGE_LOAD_SPAN_ID', true),

        /*
        | Distributed Tracing Origins
        | Origins to include in distributed tracing.
        | Use ['*'] to include all origins or specify specific origins.
        */
        'distributed_tracing_origins' => [],

        /*
        | CDN URL for Elastic APM RUM Agent
        | The URL to load the Elastic APM RUM agent from CDN.
        */
        'cdn_url' => env('ELASTIC_APM_RUM_CDN_URL', 'https://unpkg.com/@elastic/apm-rum@5.16.0/dist/bundles/elastic-apm-rum.umd.min.js'),
    ],
];
