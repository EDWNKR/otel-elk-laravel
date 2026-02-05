<?php

namespace Edwinekr\OtelElkLaravel\Traits;

use Edwinekr\OtelElkLaravel\Services\ActivityLogService;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            app(ActivityLogService::class)->logModel('created', $model, [
                'attributes' => $model->getAttributes(),
            ]);
        });

        static::updated(function ($model) {
            app(ActivityLogService::class)->logModel('updated', $model, [
                'original' => $model->getOriginal(),
                'changes' => $model->getChanges(),
            ]);
        });

        static::deleted(function ($model) {
            app(ActivityLogService::class)->logModel('deleted', $model, [
                'attributes' => $model->getAttributes(),
            ]);
        });
    }

    /**
     * Log a custom activity for this model.
     */
    public function logActivity(string $action, array $metadata = []): void
    {
        app(ActivityLogService::class)->logModel($action, $this, $metadata);
    }
}
