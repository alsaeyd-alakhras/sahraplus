<?php

namespace App\Jobs;

use App\Models\LiveTvChannel;
use App\Services\EPGService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncChannelEPG implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes timeout
    public $tries = 1; // Don't retry on failure

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $channelId,
        public string $epgId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(EPGService $epgService): void
    {
        try {
            Log::info('SyncChannelEPG Job: Starting EPG sync', [
                'channel_id' => $this->channelId,
                'epg_id' => $this->epgId,
            ]);

            $channel = LiveTvChannel::find($this->channelId);

            if (!$channel) {
                Log::warning('SyncChannelEPG Job: Channel not found', [
                    'channel_id' => $this->channelId,
                ]);
                return;
            }

            $result = $epgService->syncSingleChannelPrograms($channel);

            Log::info('SyncChannelEPG Job: EPG sync completed', [
                'channel_id' => $this->channelId,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('SyncChannelEPG Job: Failed to sync EPG programs', [
                'channel_id' => $this->channelId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to mark job as failed
        }
    }
}
