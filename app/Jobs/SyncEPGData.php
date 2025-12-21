<?php

namespace App\Jobs;

use App\Services\EPGService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncEPGData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes

    /**
     * EPG source URL
     *
     * @var string
     */
    protected string $epgUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(?string $epgUrl = null)
    {
        // Use default EPG URL (https://epg.pw/xmltv/epg_lite.xml) or custom one
        $this->epgUrl = $epgUrl ?: config('services.epg.url');
    }

    /**
     * Execute the job.
     */
    public function handle(EPGService $epgService): void
    {
        Log::info('SyncEPGData: Job started', [
            'epg_url' => $this->epgUrl,
        ]);

        try {
            // Fetch and cache EPG channels list
            $channelsCache = $epgService->fetchAndCacheChannelsList($this->epgUrl);
            
            Log::info('SyncEPGData: EPG channels cached', [
                'total_channels' => $channelsCache['total_channels'] ?? 0,
            ]);

            // Update daily schedule using epg_id from channels
            $results = $epgService->updateDailySchedule($this->epgUrl);

            Log::info('SyncEPGData: Job completed successfully', $results);

            // Clean old programs (keep last 7 days)
            $deleted = $epgService->cleanOldPrograms(7);

            Log::info('SyncEPGData: Old programs cleaned', [
                'deleted_count' => $deleted,
            ]);
        } catch (\Exception $e) {
            Log::error('SyncEPGData: Job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncEPGData: Job permanently failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
