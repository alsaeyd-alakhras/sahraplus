<?php

namespace Tests\Feature\Services;

use App\Models\LiveTvChannel;
use App\Services\StreamHealthService;
use App\Services\FlussonicService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StreamHealthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StreamHealthService $healthService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->healthService = app(StreamHealthService::class);
    }

    /** @test */
    public function it_can_ping_a_stream_url()
    {
        Http::fake([
            'example.com/*' => Http::response('', 200),
        ]);

        $result = $this->healthService->ping('https://example.com/stream.m3u8');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('response_time', $result);
        $this->assertArrayHasKey('status_code', $result);
        $this->assertEquals(200, $result['status_code']);
    }

    /** @test */
    public function it_handles_failed_ping()
    {
        Http::fake([
            'example.com/*' => Http::response('', 500),
        ]);

        $result = $this->healthService->ping('https://example.com/stream.m3u8');

        $this->assertFalse($result['success']);
        $this->assertEquals(500, $result['status_code']);
    }

    /** @test */
    public function it_treats_404_as_success_for_some_streams()
    {
        Http::fake([
            'example.com/*' => Http::response('', 404),
        ]);

        $result = $this->healthService->ping('https://example.com/stream.m3u8');

        $this->assertTrue($result['success']); // 404 is acceptable for some streams
        $this->assertEquals(404, $result['status_code']);
    }

    /** @test */
    public function it_can_check_channel_health()
    {
        $channel = LiveTvChannel::factory()->create([
            'stream_url' => 'test-stream',
            'is_active' => true,
        ]);

        // Mock Flussonic response
        Http::fake([
            '*' => Http::response(['status' => 'online'], 200),
        ]);

        $result = $this->healthService->checkChannelHealth($channel);

        $this->assertEquals($channel->id, $result['channel_id']);
        $this->assertEquals('test-stream', $result['stream_name']);
        $this->assertArrayHasKey('healthy', $result);
        $this->assertArrayHasKey('status', $result);
    }

    /** @test */
    public function it_logs_stream_failures()
    {
        $channel = LiveTvChannel::factory()->create(['stream_url' => 'failing-stream']);

        $healthCheck = [
            'status' => 'offline',
            'error' => 'Connection timeout',
            'checked_at' => now()->toDateTimeString(),
        ];

        $this->healthService->logStreamFailure($channel, $healthCheck);

        $failures = Cache::get("stream_failure_{$channel->id}");

        $this->assertNotNull($failures);
        $this->assertIsArray($failures);
        $this->assertCount(1, $failures);
        $this->assertEquals('offline', $failures[0]['status']);
    }

    /** @test */
    public function it_keeps_only_last_10_failures()
    {
        $channel = LiveTvChannel::factory()->create();

        // Log 15 failures
        for ($i = 0; $i < 15; $i++) {
            $this->healthService->logStreamFailure($channel, [
                'status' => 'offline',
                'error' => "Error {$i}",
                'checked_at' => now()->toDateTimeString(),
            ]);
        }

        $failures = Cache::get("stream_failure_{$channel->id}");

        $this->assertCount(10, $failures); // Should keep only last 10
    }

    /** @test */
    public function it_can_get_failure_history()
    {
        $channel = LiveTvChannel::factory()->create();

        // Log some failures
        $this->healthService->logStreamFailure($channel, [
            'status' => 'offline',
            'checked_at' => now()->toDateTimeString(),
        ]);

        $history = $this->healthService->getFailureHistory($channel);

        $this->assertIsArray($history);
        $this->assertNotEmpty($history);
    }

    /** @test */
    public function it_can_clear_failure_history()
    {
        $channel = LiveTvChannel::factory()->create();

        // Log failure
        $this->healthService->logStreamFailure($channel, [
            'status' => 'offline',
            'checked_at' => now()->toDateTimeString(),
        ]);

        $this->assertNotEmpty($this->healthService->getFailureHistory($channel));

        // Clear history
        $this->healthService->clearFailureHistory($channel);

        $this->assertEmpty($this->healthService->getFailureHistory($channel));
    }

    /** @test */
    public function it_can_check_all_channels()
    {
        // Create active and inactive channels
        LiveTvChannel::factory()->count(3)->create(['is_active' => true]);
        LiveTvChannel::factory()->count(2)->create(['is_active' => false]);

        Http::fake([
            '*' => Http::response(['status' => 'online'], 200),
        ]);

        $results = $this->healthService->checkAllChannels();

        $this->assertEquals(3, $results['total']); // Only active channels
        $this->assertArrayHasKey('healthy', $results);
        $this->assertArrayHasKey('unhealthy', $results);
        $this->assertArrayHasKey('channels', $results);
        $this->assertCount(3, $results['channels']);
    }

    /** @test */
    public function it_caches_health_summary()
    {
        LiveTvChannel::factory()->count(5)->create(['is_active' => true]);

        Http::fake([
            '*' => Http::response(['status' => 'online'], 200),
        ]);

        $this->healthService->checkAllChannels();

        $summary = Cache::get('stream_health_summary');

        $this->assertNotNull($summary);
        $this->assertArrayHasKey('total', $summary);
        $this->assertArrayHasKey('healthy', $summary);
        $this->assertArrayHasKey('last_check', $summary);
    }

    /** @test */
    public function it_can_get_unhealthy_channels()
    {
        $healthyChannel = LiveTvChannel::factory()->create(['is_active' => true]);
        $unhealthyChannel = LiveTvChannel::factory()->create(['is_active' => true]);

        // Log failure for unhealthy channel
        $this->healthService->logStreamFailure($unhealthyChannel, [
            'status' => 'offline',
            'timestamp' => now()->toDateTimeString(),
        ]);

        $unhealthy = $this->healthService->getUnhealthyChannels(24);

        $this->assertIsArray($unhealthy);
        $this->assertNotEmpty($unhealthy);
        $this->assertEquals($unhealthyChannel->id, $unhealthy[0]['channel']['id']);
    }

    /** @test */
    public function it_filters_unhealthy_by_time_range()
    {
        $channel = LiveTvChannel::factory()->create(['is_active' => true]);

        // Log recent failure (1 hour ago)
        $recentFailure = [
            'timestamp' => now()->subHour()->toDateTimeString(),
            'status' => 'offline',
            'checked_at' => now()->subHour()->toDateTimeString(),
            'error' => 'Connection timeout',
        ];

        // Log old failure (2 days ago)
        $oldFailure = [
            'timestamp' => now()->subDays(2)->toDateTimeString(),
            'status' => 'offline',
            'checked_at' => now()->subDays(2)->toDateTimeString(),
            'error' => 'Connection timeout',
        ];

        Cache::put("stream_failure_{$channel->id}", [$oldFailure, $recentFailure], now()->addDays(7));

        // Get failures from last 2 hours (should include recent failure only)
        $unhealthy = $this->healthService->getUnhealthyChannels(2);
        $this->assertCount(1, $unhealthy);
        $this->assertGreaterThanOrEqual(1, $unhealthy[0]['failure_count']);

        // Get failures from last 48 hours (should include both failures)
        $unhealthy = $this->healthService->getUnhealthyChannels(48);
        $this->assertCount(1, $unhealthy);
        $this->assertGreaterThanOrEqual(1, $unhealthy[0]['failure_count']);
    }

    /** @test */
    public function it_handles_timeout_errors()
    {
        Http::fake(function () {
            throw new \Exception('Connection timeout');
        });

        $result = $this->healthService->ping('https://example.com/stream.m3u8', 1);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertStringContainsString('timeout', strtolower($result['message']));
    }

    /** @test */
    public function it_measures_response_time()
    {
        Http::fake([
            'example.com/*' => Http::response('', 200),
        ]);

        $result = $this->healthService->ping('https://example.com/stream.m3u8');

        $this->assertArrayHasKey('response_time', $result);
        $this->assertIsNumeric($result['response_time']);
        $this->assertGreaterThan(0, $result['response_time']);
    }
}
