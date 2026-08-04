<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

final class FoundationConfigurationTest extends TestCase
{
    public function test_backend_uses_postgresql_and_redis_for_foundation_services(): void
    {
        $this->assertSame('pgsql', config('database.default'));
        $this->assertSame('redis', config('cache.default'));
        $this->assertSame('redis', config('queue.default'));

        $this->assertNotEmpty(DB::select('select 1 as healthcheck'));
        $this->assertNotEmpty(Redis::connection()->ping());

        Cache::put('foundation-cache-healthcheck', 'ok', 10);

        $this->assertSame('ok', Cache::get('foundation-cache-healthcheck'));
        $this->assertIsInt(Queue::connection('redis')->size());
    }
}
