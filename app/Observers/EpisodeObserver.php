<?php

namespace App\Observers;


use App\Models\Episode;
use App\Services\ActivityLogService;

class EpisodeObserver
{
    public function created(Episode $episod): void
    {
        ActivityLogService::log(
            'Created',
            'Episod',
            "تم إضافة الحلقة رقم {$episod->episode_number} للموسم {$episod->season_id}.",
            null,
            $episod->toArray()
        );
    }

    /**
     * Handle the Episod "updated" event.
     */
    public function updated(Episode $episod): void
    {
        //
    }

    /**
     * Handle the Episod "deleted" event.
     */
    public function deleted(Episode $episod): void
    {
        ActivityLogService::log(
            'Deleted',
            'Episod',
            "تم حذف الحلقة رقم {$episod->episode_number} للموسم {$episod->season_id}.",
            $episod->toArray(),
            null
        );
    }

    /**
     * Handle the Episod "restored" event.
     */
    public function restored(Episode $episod): void
    {
        //
    }

    /**
     * Handle the Episod "force deleted" event.
     */
    public function forceDeleted(Episode $episod): void
    {
        //
    }
}
