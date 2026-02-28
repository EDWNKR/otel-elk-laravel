<?php

namespace Edwinekr\OtelElkLaravel;

use Edwinekr\OtelElkLaravel\Helpers\RumHelper;
use Edwinekr\OtelElkLaravel\Listeners\AuthActivityListener;
use Edwinekr\OtelElkLaravel\Middleware\ActivityLogMiddleware;
use Edwinekr\OtelElkLaravel\Services\ActivityLogService;
use Edwinekr\OtelElkLaravel\View\Components\RumScript;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class OtelElkServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/activity_log.php',
            'activity_log'
        );

        // Register the service as singleton
        $this->app->singleton(ActivityLogService::class, function ($app) {
            return new ActivityLogService();
        });

        // Register alias for easier access
        $this->app->alias(ActivityLogService::class, 'activity-log');

        // Register RUM Helper
        $this->app->singleton(RumHelper::class, function ($app) {
            return new RumHelper();
        });

        // Register alias for RUM
        $this->app->alias(RumHelper::class, 'rum');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/activity_log.php' => config_path('activity_log.php'),
        ], 'otel-elk-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/otel-elk'),
        ], 'otel-elk-views');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'otel-elk');

        // Register Blade component
        Blade::component('rum-script', RumScript::class);

        // Register middleware alias
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('activity-log', ActivityLogMiddleware::class);

        // Auto-register global middleware if enabled
        if (config('activity_log.auto_register_middleware', true)) {
            $this->registerGlobalMiddleware();
        }

        // Register auth event listeners if enabled
        if (config('activity_log.log_auth_events', true)) {
            $this->registerAuthListeners();
        }
    }

    /**
     * Register the middleware globally.
     */
    protected function registerGlobalMiddleware(): void
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(ActivityLogMiddleware::class);
    }

    /**
     * Register authentication event listeners.
     */
    protected function registerAuthListeners(): void
    {
        Event::listen(Login::class, [AuthActivityListener::class, 'handleLogin']);
        Event::listen(Logout::class, [AuthActivityListener::class, 'handleLogout']);
        Event::listen(Failed::class, [AuthActivityListener::class, 'handleFailed']);
        Event::listen(Lockout::class, [AuthActivityListener::class, 'handleLockout']);
        Event::listen(Registered::class, [AuthActivityListener::class, 'handleRegistered']);
        Event::listen(PasswordReset::class, [AuthActivityListener::class, 'handlePasswordReset']);
    }
}
