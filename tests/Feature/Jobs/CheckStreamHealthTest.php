<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CheckStreamHealth;
use App\Models\LiveTvChannel;
use App\Services\StreamHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CheckStreamHealthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_dispatched()
    {
        $this->expectNotToPerformAssertions();
        
        CheckStreamHealth::dispatch();
    }

    /** @test */
    public function it_checks_all_active_channels()
    {
        // Create active channels
        LiveTvChannel::factory()->count(3)->create(['is_active' => true]);
        
        // Create inactive channel (should be skipped)
        LiveTvChannel::factory()->create(['is_active' => false]);

        Http::fake([
            '*' => Http::response(['status' => 'online'], 200),
        ]);

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));

        // Should have checked 3 channels
        $summary = Cache::get('stream_health_summary');
        $this->assertNotNull($summary);
        $this->assertEquals(3, $summary['total']);
    }

    /** @test */
    public function it_logs_unhealthy_channels()
    {
        $channel = LiveTvChannel::factory()->create([
            'name_en' => 'Test Channel',
            'is_active' => true,
        ]);

        Http::fake([
            '*' => Http::response('', 500),
        ]);

        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')->atLeast()->once();

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));
    }

    /** @test */
    public function it_caches_health_results()
    {
        LiveTvChannel::factory()->count(5)->create(['is_active' => true]);

        Http::fake([
            '*' => Http::response(['status' => 'online'], 200),
        ]);

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));

        $summary = Cache::get('stream_health_summary');

        $this->assertNotNull($summary);
        $this->assertArrayHasKey('total', $summary);
        $this->assertArrayHasKey('healthy', $summary);
        $this->assertArrayHasKey('unhealthy', $summary);
        $this->assertArrayHasKey('last_check', $summary);
    }

    /** @test */
    public function it_tracks_failure_history()
    {
        $channel = LiveTvChannel::factory()->create(['is_active' => true]);

        Http::fake([
            '*' => Http::response('', 500),
        ]);

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));

        $failures = Cache::get("stream_failure_{$channel->id}");

        $this->assertNotNull($failures);
        $this->assertIsArray($failures);
        $this->assertNotEmpty($failures);
    }

    /** @test */
    public function it_handles_no_channels_gracefully()
    {
        // No channels in database

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));

        $summary = Cache::get('stream_health_summary');

        $this->assertNotNull($summary);
        $this->assertEquals(0, $summary['total']);
    }

    /** @test */
    public function it_continues_on_individual_channel_errors()
    {
        $channel1 = LiveTvChannel::factory()->create(['is_active' => true]);
        $channel2 = LiveTvChannel::factory()->create(['is_active' => true]);
        $channel3 = LiveTvChannel::factory()->create(['is_active' => true]);

        Http::fake([
            "*{$channel1->stream_url}*" => function () {
                throw new \Exception('Connection error');
            },
            "*{$channel2->stream_url}*" => Http::response(['status' => 'online'], 200),
            "*{$channel3->stream_url}*" => Http::response(['status' => 'online'], 200),
        ]);

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));

        // Job should complete despite error on channel1
        $summary = Cache::get('stream_health_summary');
        $this->assertNotNull($summary);
    }

    /** @test */
    public function it_has_correct_retry_configuration()
    {
        $job = new CheckStreamHealth();

        $this->assertEquals(2, $job->tries);
        $this->assertEquals(300, $job->timeout); // 5 minutes
    }

    /** @test */
    public function it_logs_completion_summary()
    {
        LiveTvChannel::factory()->count(5)->create(['is_active' => true]);

        Http::fake([
            '*' => Http::response(['status' => 'online'], 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));
    }

    /** @test */
    public function it_identifies_healthy_vs_unhealthy_channels()
    {
        $healthyChannel = LiveTvChannel::factory()->create([
            'name_en' => 'Healthy Channel',
            'is_active' => true,
        ]);

        $unhealthyChannel = LiveTvChannel::factory()->create([
            'name_en' => 'Unhealthy Channel',
            'is_active' => true,
        ]);

        Http::fake([
            "*{$healthyChannel->stream_url}*" => Http::response(['status' => 'online'], 200),
            "*{$unhealthyChannel->stream_url}*" => Http::response('', 500),
        ]);

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));

        $summary = Cache::get('stream_health_summary');

        $this->assertEquals(2, $summary['total']);
        $this->assertEquals(1, $summary['healthy']);
        $this->assertEquals(1, $summary['unhealthy']);
    }

    /** @test */
    public function it_updates_last_check_timestamp()
    {
        LiveTvChannel::factory()->create(['is_active' => true]);

        Http::fake([
            '*' => Http::response(['status' => 'online'], 200),
        ]);

        $beforeTime = now();

        $job = new CheckStreamHealth();
        $job->handle(app(StreamHealthService::class));

        $summary = Cache::get('stream_health_summary');

        $this->assertNotNull($summary['last_check']);
        $this->assertGreaterThanOrEqual(
            $beforeTime->timestamp,
            \Carbon\Carbon::parse($summary['last_check'])->timestamp
        );
    }

    /** @test */
    public function it_can_trigger_alerts_for_repeated_failures()
    {
        $channel = LiveTvChannel::factory()->create(['is_active' => true]);

        Http::fake([
            '*' => Http::response('', 500),
        ]);

        // Run job multiple times to create repeated failures
        for ($i = 0; $i < 3; $i++) {
            $job = new CheckStreamHealth();
            $job->handle(app(StreamHealthService::class));
        }

        $failures = Cache::get("stream_failure_{$channel->id}");

        $this->assertGreaterThanOrEqual(3, count($failures));
    }

    /** @test */
    public function it_respects_timeout_setting()
    {
        $job = new CheckStreamHealth();

        $this->assertObjectHasProperty('timeout', $job);
        $this->assertEquals(300, $job->timeout);
    }
}
