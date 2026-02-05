<?php

namespace Edwinekr\OtelElkLaravel\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogService
{
    private string $endpoint;
    private bool $enabled;
    private string $application;
    private string $environment;
    private int $timeout;
    private bool $async;
    private string $userResolver;

    public function __construct()
    {
        $this->endpoint = config('activity_log.endpoint', 'http://localhost:5044');
        $this->enabled = config('activity_log.enabled', true);
        $this->application = config('activity_log.application', config('app.name'));
        $this->environment = config('activity_log.environment', config('app.env'));
        $this->timeout = config('activity_log.timeout', 2);
        $this->async = config('activity_log.async', true);
        $this->userResolver = config('activity_log.user_resolver', 'default');
    }

    /**
     * Log HTTP request activity
     */
    public function logRequest(Request $request, Response $response, float $responseTimeMs): void
    {
        if (!$this->enabled) {
            return;
        }

        if ($this->shouldExclude($request, $response)) {
            return;
        }

        $data = $this->buildRequestLogData($request, $response, $responseTimeMs);

        $this->send($data);
    }

    /**
     * Log custom activity
     */
    public function log(
        string $action,
        string $entity,
        ?int $entityId = null,
        array $metadata = [],
        ?string $level = 'info'
    ): void {
        if (!$this->enabled) {
            return;
        }

        $request = request();
        $user = $this->resolveUser($request);

        $data = [
            // Activity info
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'level' => $level,
            'metadata' => $metadata,

            // User info
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? null,
            'user_email' => $user['email'] ?? null,
            'username' => $user['username'] ?? null,

            // Client info
            'ip' => $request->ip(),
            'useragent' => $request->userAgent(),

            // Application context
            'application' => $this->application,
            'environment' => $this->environment,
            'log_type' => 'activity',

            // Timestamp
            'timestamp' => now()->toIso8601String(),

            // Trace info
            'trace_id' => $request->header('X-Trace-ID', Str::uuid()->toString()),
            'request_id' => $request->header('X-Request-ID', Str::uuid()->toString()),
        ];

        $this->send($data);
    }

    /**
     * Log authentication events
     */
    public function logAuth(string $action, ?int $userId = null, array $metadata = []): void
    {
        $this->log(
            action: "auth.{$action}",
            entity: 'User',
            entityId: $userId,
            metadata: $metadata,
            level: in_array($action, ['failed', 'lockout']) ? 'warning' : 'info'
        );
    }

    /**
     * Log model events
     */
    public function logModel(string $action, $model, array $changes = []): void
    {
        $this->log(
            action: "model.{$action}",
            entity: class_basename($model),
            entityId: $model->getKey(),
            metadata: [
                'changes' => $changes,
                'model_class' => get_class($model),
            ]
        );
    }

    /**
     * Resolve user based on configuration
     */
    protected function resolveUser(Request $request): array
    {
        $userFields = config('activity_log.user_fields', ['id', 'name', 'email', 'username']);

        switch ($this->userResolver) {
            case 'session':
                return $this->resolveUserFromSession($userFields);

            case 'custom':
                return $this->resolveUserCustom($request, $userFields);

            case 'default':
            default:
                return $this->resolveUserDefault($request, $userFields);
        }
    }

    /**
     * Resolve user from authenticated user (default)
     */
    protected function resolveUserDefault(Request $request, array $fields): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $user->{$field} ?? null;
        }

        return $data;
    }

    /**
     * Resolve user from session
     */
    protected function resolveUserFromSession(array $fields): array
    {
        $sessionKey = config('activity_log.session_user_key', 'user');
        $sessionUser = Session::get($sessionKey);

        if (!$sessionUser) {
            return [];
        }

        if (is_array($sessionUser)) {
            return array_intersect_key($sessionUser, array_flip($fields));
        }

        if (is_object($sessionUser)) {
            $data = [];
            foreach ($fields as $field) {
                $data[$field] = $sessionUser->{$field} ?? null;
            }
            return $data;
        }

        return [];
    }

    /**
     * Resolve user with custom logic (can be extended)
     */
    protected function resolveUserCustom(Request $request, array $fields): array
    {
        // First try default auth
        $data = $this->resolveUserDefault($request, $fields);

        if (!empty($data['id'])) {
            return $data;
        }

        // Then try session
        return $this->resolveUserFromSession($fields);
    }

    /**
     * Build log data from HTTP request
     */
    private function buildRequestLogData(Request $request, Response $response, float $responseTimeMs): array
    {
        $user = $this->resolveUser($request);
        $route = $request->route();

        $data = [
            // Request info
            'action' => 'http.request',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => '/' . ltrim($request->path(), '/'),
            'route_name' => $route?->getName(),
            'route_action' => $route?->getActionName(),

            // Response info
            'response_code' => $response->getStatusCode(),
            'response_time' => round($responseTimeMs) . 'ms',
            'response_size' => strlen($response->getContent()),

            // Client info
            'ip' => $request->ip(),
            'useragent' => $request->userAgent(),
            'referer' => $request->header('Referer'),
            'accept_language' => $request->header('Accept-Language'),

            // User info
            'user_id' => $user['id'] ?? null,
            'user_email' => $user['email'] ?? null,
            'user_name' => $user['name'] ?? null,
            'username' => $user['username'] ?? null,

            // Request metadata
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'is_ajax' => $request->ajax(),
            'is_json' => $request->isJson(),
            'is_secure' => $request->secure(),

            // Application context
            'application' => $this->application,
            'environment' => $this->environment,
            'log_type' => 'http_request',

            // Timestamp
            'timestamp' => now()->toIso8601String(),

            // Trace info
            'trace_id' => $request->header('X-Trace-ID', Str::uuid()->toString()),
            'request_id' => $request->header('X-Request-ID', Str::uuid()->toString()),
            'span_id' => Str::uuid()->toString(),
        ];

        // Add request body if configured
        if (config('activity_log.log_request_body', false)) {
            $data['request_body'] = $this->sanitizeRequestBody($request->all());
        }

        // Add query parameters
        if ($request->query()) {
            $data['query_params'] = $this->sanitizeRequestBody($request->query());
        }

        return $data;
    }

    /**
     * Sanitize request body by masking sensitive fields
     */
    private function sanitizeRequestBody(array $data): array
    {
        $sensitiveFields = config('activity_log.sensitive_fields', []);

        array_walk_recursive($data, function (&$value, $key) use ($sensitiveFields) {
            if (in_array(strtolower($key), array_map('strtolower', $sensitiveFields))) {
                $value = '***REDACTED***';
            }
        });

        return $data;
    }

    /**
     * Check if request should be excluded from logging
     */
    private function shouldExclude(Request $request, ?Response $response = null): bool
    {
        // Check excluded methods
        if (in_array($request->method(), config('activity_log.excluded_methods', []))) {
            return true;
        }

        // Check if AJAX requests should be excluded
        if (config('activity_log.exclude_ajax', true) && $request->ajax()) {
            return true;
        }

        // Check if JSON requests should be excluded
        if (config('activity_log.exclude_json_requests', false) && $request->wantsJson()) {
            return true;
        }

        // Check if redirects should be excluded
        if ($response && config('activity_log.exclude_redirects', true)) {
            $statusCode = $response->getStatusCode();
            if ($statusCode >= 300 && $statusCode < 400) {
                return true;
            }
        }

        // Check if static assets should be excluded
        if (config('activity_log.exclude_static_assets', true)) {
            if ($this->isStaticAsset($request)) {
                return true;
            }
        }

        // Check if only HTML requests should be logged
        if (config('activity_log.only_html_requests', true)) {
            $acceptHeader = $request->header('Accept', '');
            if (!Str::contains($acceptHeader, ['text/html', '*/*'])) {
                return true;
            }
            // Also check if it's a CSS/JS preload request
            if (Str::startsWith($acceptHeader, ['text/css', 'application/javascript', 'image/'])) {
                return true;
            }
        }

        // Check route-related exclusions
        $route = $request->route();
        $routeName = $route?->getName();
        $routeAction = $route?->getActionName();

        // Check if only controller actions should be logged
        if (config('activity_log.only_controller_actions', true)) {
            if (!$routeAction || $routeAction === 'Closure' || !Str::contains($routeAction, '@')) {
                return true;
            }
        }

        // Check if only named routes should be logged
        if (config('activity_log.only_named_routes', true) && empty($routeName)) {
            return true;
        }

        // Check included routes (whitelist)
        $includedRoutes = config('activity_log.included_routes', []);
        if (!empty($includedRoutes) && $routeName) {
            $isIncluded = false;
            foreach ($includedRoutes as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    $isIncluded = true;
                    break;
                }
            }
            if (!$isIncluded) {
                return true;
            }
        }

        // Check excluded routes (blacklist)
        if ($routeName) {
            foreach (config('activity_log.excluded_routes', []) as $excludedRoute) {
                if (Str::is($excludedRoute, $routeName)) {
                    return true;
                }
            }
        }

        // Check excluded paths
        $path = $request->path();
        foreach (config('activity_log.excluded_paths', []) as $excludedPath) {
            if (Str::is($excludedPath, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request is for a static asset
     */
    private function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        $extensions = config('activity_log.static_asset_extensions', []);
        
        // Check file extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, $extensions)) {
            return true;
        }

        // Check common asset paths
        $assetPaths = ['css/*', 'js/*', 'images/*', 'img/*', 'fonts/*', 'assets/*', 'build/*', 'vendor/*', 'storage/*'];
        foreach ($assetPaths as $assetPath) {
            if (Str::is($assetPath, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send log data to Logstash
     */
    private function send(array $data): void
    {
        if ($this->async) {
            $this->sendAsync($data);
        } else {
            $this->sendSync($data);
        }
    }

    /**
     * Send synchronously
     */
    private function sendSync(array $data): void
    {
        try {
            $retryConfig = config('activity_log.retry', ['times' => 2, 'sleep' => 100]);

            Http::timeout($this->timeout)
                ->retry($retryConfig['times'], $retryConfig['sleep'])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint, $data);
        } catch (\Exception $e) {
            Log::warning('Activity log failed: ' . $e->getMessage(), [
                'endpoint' => $this->endpoint,
                'data' => $data,
            ]);
        }
    }

    /**
     * Send asynchronously (non-blocking)
     */
    private function sendAsync(array $data): void
    {
        // Use dispatch afterResponse to not block the HTTP response
        dispatch(function () use ($data) {
            $this->sendSync($data);
        })->afterResponse();
    }

    /**
     * Check if logging is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get the configured endpoint
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the application name
     */
    public function getApplication(): string
    {
        return $this->application;
    }

    /**
     * Get the environment
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
