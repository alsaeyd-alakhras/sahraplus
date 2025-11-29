<?php

namespace App\Services;

use App\Models\LiveTvChannel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StreamHealthService
{
    protected FlussonicService $flussonicService;

    public function __construct(FlussonicService $flussonicService)
    {
        $this->flussonicService = $flussonicService;
    }

    /**
     * Ping a stream URL to check if it's reachable (lightweight check)
     *
     * @param string $url Full stream URL
     * @param int $timeout Timeout in seconds
     * @return array ['success' => bool, 'response_time' => float|null, 'status_code' => int|null]
     */
    public function ping(string $url, int $timeout = 3): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($timeout)
                ->withOptions(['allow_redirects' => true])
                ->head($url);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2); // ms

            return [
                'success' => $response->successful() || $response->status() === 404, // 404 is OK for some streams
                'response_time' => $responseTime,
                'status_code' => $response->status(),
                'message' => $response->successful() ? 'Stream reachable' : 'Stream returned error',
            ];
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'success' => false,
                'response_time' => $responseTime,
                'status_code' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check health of a specific channel's stream
     *
     * @param LiveTvChannel $channel
     * @return array ['status' => string, 'healthy' => bool, 'details' => array]
     */
    public function checkChannelHealth(LiveTvChannel $channel): array
    {
        try {
            // Generate authenticated stream URL first
            $streamData = $this->flussonicService->generateStreamUrl(
                streamName: $channel->stream_url,
                protocol: 'hls'
            );
            
            // Check stream health using the authenticated URL
            $healthCheck = $this->flussonicService->checkStreamHealth(
                $channel->stream_url,
                $streamData['url']
            );

            $isHealthy = $healthCheck['status'] === 'online';

            // Update channel health status in database
            $channel->update([
                'stream_health_status' => $healthCheck['status'],
                'stream_health_last_check' => now(),
                'stream_health_details' => $healthCheck,
            ]);

            // Log if stream is down
            if (!$isHealthy) {
                $this->logStreamFailure($channel, $healthCheck);
            }

            return [
                'channel_id' => $channel->id,
                'channel_name' => $channel->name_ar,
                'stream_name' => $channel->stream_url,
                'status' => $healthCheck['status'],
                'healthy' => $isHealthy,
                'details' => $healthCheck,
                'checked_at' => now()->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            Log::error('StreamHealth: Failed to check channel', [
                'channel_id' => $channel->id,
                'error' => $e->getMessage()
            ]);

            return [
                'channel_id' => $channel->id,
                'channel_name' => $channel->name_ar,
                'stream_name' => $channel->stream_url,
                'status' => 'error',
                'healthy' => false,
                'details' => ['error' => $e->getMessage()],
                'checked_at' => now()->toDateTimeString(),
            ];
        }
    }

    /**
     * Check health of all active channels
     *
     * @return array ['total' => int, 'healthy' => int, 'unhealthy' => int, 'channels' => array]
     */
    public function checkAllChannels(): array
    {
        $channels = LiveTvChannel::where('is_active', true)->get();

        $results = [
            'total' => $channels->count(),
            'healthy' => 0,
            'unhealthy' => 0,
            'error' => 0,
            'channels' => [],
            'checked_at' => now()->toDateTimeString(),
        ];

        foreach ($channels as $channel) {
            $health = $this->checkChannelHealth($channel);

            if ($health['healthy']) {
                $results['healthy']++;
            } elseif ($health['status'] === 'error') {
                $results['error']++;
            } else {
                $results['unhealthy']++;
            }

            $results['channels'][] = $health;
        }

        // Cache summary for dashboard
        Cache::put('stream_health_summary', [
            'total' => $results['total'],
            'healthy' => $results['healthy'],
            'unhealthy' => $results['unhealthy'],
            'error' => $results['error'],
            'last_check' => $results['checked_at'],
        ], now()->addMinutes(10));

        return $results;
    }

    /**
     * Log stream failure to database and logs
     *
     * @param LiveTvChannel $channel
     * @param array $healthCheckResult
     * @return void
     */
    public function logStreamFailure(LiveTvChannel $channel, array $healthCheckResult): void
    {
        $failureData = [
            'channel_id' => $channel->id,
            'channel_name' => $channel->name_ar,
            'stream_name' => $channel->stream_url,
            'status' => $healthCheckResult['status'] ?? 'unknown',
            'error' => $healthCheckResult['error'] ?? null,
            'checked_at' => $healthCheckResult['checked_at'] ?? now()->toDateTimeString(),
        ];

        // Log to Laravel logs
        Log::warning('StreamHealth: Channel stream is down', $failureData);

        // Cache recent failures for quick access
        $cacheKey = "stream_failure_{$channel->id}";
        $failures = Cache::get($cacheKey, []);

        $failures[] = [
            'timestamp' => now()->toDateTimeString(),
            'status' => $healthCheckResult['status'],
            'error' => $healthCheckResult['error'] ?? null,
        ];

        // Keep only last 10 failures
        $failures = array_slice($failures, -10);

        Cache::put($cacheKey, $failures, now()->addDays(7));
    }

    /**
     * Get stream failure history for a channel
     *
     * @param LiveTvChannel $channel
     * @return array
     */
    public function getFailureHistory(LiveTvChannel $channel): array
    {
        $cacheKey = "stream_failure_{$channel->id}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Get channels with recent failures
     *
     * @param int $hoursAgo Check failures in last X hours
     * @return array
     */
    public function getUnhealthyChannels(int $hoursAgo = 24): array
    {
        $channels = LiveTvChannel::where('is_active', true)->get();
        $unhealthy = [];

        foreach ($channels as $channel) {
            $failures = $this->getFailureHistory($channel);

            if (!empty($failures)) {
                $recentFailures = collect($failures)->filter(function ($failure) use ($hoursAgo) {
                    $failureTime = \Carbon\Carbon::parse($failure['timestamp']);
                    return $failureTime->greaterThan(now()->subHours($hoursAgo));
                });

                if ($recentFailures->isNotEmpty()) {
                    $unhealthy[] = [
                        'channel' => [
                            'id' => $channel->id,
                            'name_ar' => $channel->name_ar,
                            'name_en' => $channel->name_en,
                            'stream_name' => $channel->stream_url,
                        ],
                        'failure_count' => $recentFailures->count(),
                        'last_failure' => $recentFailures->last(),
                        'recent_failures' => $recentFailures->values()->toArray(),
                    ];
                }
            }
        }

        return $unhealthy;
    }

    /**
     * Test connection to Flussonic server
     *
     * @return array
     */
    public function testServerConnection(): array
    {
        return $this->flussonicService->testConnection();
    }

    /**
     * Get health summary from cache (for dashboard)
     *
     * @return array|null
     */
    public function getHealthSummary(): ?array
    {
        return Cache::get('stream_health_summary');
    }

    /**
     * Clear failure history for a channel
     *
     * @param LiveTvChannel $channel
     * @return void
     */
    public function clearFailureHistory(LiveTvChannel $channel): void
    {
        $cacheKey = "stream_failure_{$channel->id}";
        Cache::forget($cacheKey);

        Log::info('StreamHealth: Cleared failure history', [
            'channel_id' => $channel->id,
            'channel_name' => $channel->name_ar,
        ]);
    }

    /**
     * Auto-failover logic (placeholder for future implementation)
     * 
     * This method can be expanded to support multiple stream URLs per channel
     * and automatically switch to backup streams when primary fails
     *
     * @param LiveTvChannel $channel
     * @return array|null ['url' => string, 'type' => 'primary|backup']
     */
    public function getWorkingStreamUrl(LiveTvChannel $channel): ?array
    {
        // Current implementation: Single stream URL
        // Future: Support multiple URLs with priority

        $health = $this->checkChannelHealth($channel);

        if ($health['healthy']) {
            return [
                'url' => $channel->stream_url,
                'type' => 'primary',
                'status' => 'healthy',
            ];
        }
        // TODO: Implement backup stream logic

        return null;
    }
}
