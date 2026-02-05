<?php

namespace Edwinekr\OtelElkLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void logRequest(\Illuminate\Http\Request $request, \Symfony\Component\HttpFoundation\Response $response, float $responseTimeMs)
 * @method static void log(string $action, string $entity, ?int $entityId = null, array $metadata = [], ?string $level = 'info')
 * @method static void logAuth(string $action, ?int $userId = null, array $metadata = [])
 * @method static void logModel(string $action, $model, array $changes = [])
 * @method static bool isEnabled()
 * @method static string getEndpoint()
 * @method static string getApplication()
 * @method static string getEnvironment()
 *
 * @see \Edwinekr\OtelElkLaravel\Services\ActivityLogService
 */
class ActivityLog extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Edwinekr\OtelElkLaravel\Services\ActivityLogService::class;
    }
}
