<?php

namespace Tests\Feature;

use Tests\TestCase;

final class HealthTest extends TestCase
{
    public function test_api_v1_health_returns_operational_payload(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertExactJson([
                'status' => 'ok',
                'service' => 'jsdesign-api',
                'api_version' => 'v1',
                'checks' => [
                    'app' => 'ok',
                    'database' => 'ok',
                    'redis' => 'ok',
                ],
            ]);
    }
}
