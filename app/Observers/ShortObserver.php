<?php

namespace App\Observers;

use App\Models\Short;
use App\Services\ActivityLogService;

class ShortObserver
{
    public function created(Short $short): void
    {
        ActivityLogService::log(
            'Created',
            'Short',
            "تمت إضافة Short: {$short->title}.",
            null,
            $short->toArray()
        );
    }

    /**
     * Handle the Short "updated" event.
     */
    public function updated(Short $short): void
    {
        //
    }

    /**
     * Handle the Short "deleted" event.
     */
    public function deleted(Short $short): void
    {
        ActivityLogService::log(
            'Deleted',
            'Short',
            "تم حذف Short: {$short->title}.",
            $short->toArray(),
            null
        );
    }

    /**
     * Handle the Short "restored" event.
     */
    public function restored(Short $short): void
    {
        //
    }

    /**
     * Handle the Short "force deleted" event.
     */
    public function forceDeleted(Short $short): void
    {
        //
    }
}
