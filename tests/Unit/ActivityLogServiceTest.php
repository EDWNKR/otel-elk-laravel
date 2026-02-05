<?php

namespace Edwinekr\OtelElkLaravel\Tests\Unit;

use Edwinekr\OtelElkLaravel\Services\ActivityLogService;
use Edwinekr\OtelElkLaravel\Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    private ActivityLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ActivityLogService::class);
    }

    public function test_service_is_registered(): void
    {
        $this->assertInstanceOf(ActivityLogService::class, $this->service);
    }

    public function test_is_enabled_returns_boolean(): void
    {
        $this->assertIsBool($this->service->isEnabled());
    }

    public function test_get_endpoint_returns_string(): void
    {
        $this->assertIsString($this->service->getEndpoint());
        $this->assertEquals('http://localhost:5044', $this->service->getEndpoint());
    }

    public function test_get_application_returns_string(): void
    {
        $this->assertIsString($this->service->getApplication());
    }

    public function test_get_environment_returns_string(): void
    {
        $this->assertIsString($this->service->getEnvironment());
    }
}
