<?php

namespace App\Jobs;

use App\Models\WatchProgres;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateViewingStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // تحديث كل المشاهدات قيد التقدم
        WatchProgres::inProgress()->each(function ($progress) {
            $watchedSeconds = $progress->watched_seconds;
            $totalSeconds = $progress->total_seconds;

            $newPercentage = ($totalSeconds > 0) ? ($watchedSeconds / $totalSeconds) * 100 : 0;
            $isCompleted = $newPercentage >= 85;

            $progress->update([
                'progress_percentage' => $newPercentage,
                'is_completed' => $isCompleted,
                'last_watched_at' => now()
            ]);
        });

        Log::info("✅ Updated Watch Progress for all in-progress items at " . now());
    }
}
