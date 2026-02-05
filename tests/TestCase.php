<?php

namespace Edwinekr\OtelElkLaravel\Tests;

use Edwinekr\OtelElkLaravel\OtelElkServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            OtelElkServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Set up configuration for testing
        $app['config']->set('activity_log.enabled', true);
        $app['config']->set('activity_log.endpoint', 'http://localhost:5044');
        $app['config']->set('activity_log.async', false); // Sync for testing
    }
}
