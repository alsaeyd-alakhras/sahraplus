<?php

namespace App\Jobs;

use App\Models\UserRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessUserRating implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // معالجة كل التقييمات الجديدة أو غير المعالجة
        UserRating::whereNull('reviewed_at')->each(function ($rating) {
            // مثال: الموافقة على التقييم تلقائيًا
            $rating->update([
                'reviewed_at' => now(),
                'status' => 'approved'
            ]);
        });

        Log::info("✅ Processed User Ratings at " . now());
    }
}
