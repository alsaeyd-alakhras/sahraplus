<?php

namespace App\Jobs;

use App\Services\StreamHealthService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckStreamHealth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * Execute the job.
     */
    public function handle(StreamHealthService $streamHealthService): void
    {
        Log::info('CheckStreamHealth: Job started');

        try {
            // Check all active channels
            $results = $streamHealthService->checkAllChannels();

            Log::info('CheckStreamHealth: Job completed', [
                'total_channels' => $results['total'],
                'healthy' => $results['healthy'],
                'unhealthy' => $results['unhealthy'],
                'errors' => $results['error'],
            ]);

            // Send alert if there are unhealthy channels
            if ($results['unhealthy'] > 0 || $results['error'] > 0) {
                $this->sendAlert($results);
            }
        } catch (\Exception $e) {
            Log::error('CheckStreamHealth: Job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Send alert about unhealthy channels
     *
     * @param array $results
     * @return void
     */
    protected function sendAlert(array $results): void
    {
        $unhealthyChannels = collect($results['channels'])
            ->filter(fn($channel) => !$channel['healthy'])
            ->take(10); // Limit to first 10

        $message = sprintf(
            "⚠️ Stream Health Alert\n\n" .
                "Total Channels: %d\n" .
                "Healthy: %d\n" .
                "Unhealthy: %d\n" .
                "Errors: %d\n\n" .
                "Affected Channels:\n%s",
            $results['total'],
            $results['healthy'],
            $results['unhealthy'],
            $results['error'],
            $unhealthyChannels->map(function ($channel) {
                return sprintf(
                    "- %s (%s): %s",
                    $channel['channel_name'],
                    $channel['stream_name'],
                    $channel['status']
                );
            })->implode("\n")
        );

        Log::warning('CheckStreamHealth: Alert triggered', [
            'message' => $message,
            'unhealthy_count' => $results['unhealthy'],
        ]);

        // TODO: Send notifications via Email/Slack/Dashboard
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CheckStreamHealth: Job permanently failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
