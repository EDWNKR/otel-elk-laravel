<?php

namespace Edwinekr\OtelElkLaravel\Middleware;

use Closure;
use Edwinekr\OtelElkLaravel\Services\ActivityLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Record start time
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        // Calculate response time in milliseconds
        $responseTimeMs = (microtime(true) - $startTime) * 1000;

        // Log the request
        $this->activityLog->logRequest($request, $response, $responseTimeMs);

        return $response;
    }
}
