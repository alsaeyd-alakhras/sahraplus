<?php

namespace App\Observers;

use App\Models\Season;
use App\Services\ActivityLogService;

class SeasonObserver
{
    public function created(Season $season): void
    {
        ActivityLogService::log(
            'Created',
            'Season',
            "تمت إضافة الموسم رقم {$season->season_number} للمسلسل {$season->series_id}.",
            null,
            $season->toArray()
        );
    }

    /**
     * Handle the Season "updated" event.
     */
    public function updated(Season $season): void
    {
        //
    }

    /**
     * Handle the Season "deleted" event.
     */
    public function deleted(Season $season): void
    {
        ActivityLogService::log(
            'Deleted',
            'Season',
            "تم حذف الموسم رقم {$season->season_number} للمسلسل {$season->series_id}.",
            $season->toArray(),
            null
        );
    }

    /**
     * Handle the Season "restored" event.
     */
    public function restored(Season $season): void
    {
        //
    }

    /**
     * Handle the Season "force deleted" event.
     */
    public function forceDeleted(Season $season): void
    {
        //
    }
}


