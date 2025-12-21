<?php

namespace App\Observers;

use App\Models\LiveTvCategory;
use App\Services\ActivityLogService;

class LiveTvCategoryObserver
{
    /**
     * Handle the LiveTvCategory "created" event.
     */
    public function created(LiveTvCategory $category): void
    {
        ActivityLogService::log(
            'Created',
            'LiveTvCategory',
            "تم إضافة فئة قنوات: {$category->name_ar}.",
            null,
            $category->toArray()
        );
    }

    /**
     * Handle the LiveTvCategory "updated" event.
     */
    public function updated(LiveTvCategory $category): void
    {
        //
    }

    /**
     * Handle the LiveTvCategory "deleted" event.
     */
    public function deleted(LiveTvCategory $category): void
    {
        ActivityLogService::log(
            'Deleted',
            'LiveTvCategory',
            "تم حذف فئة قنوات: {$category->name_ar}.",
            $category->toArray(),
            null
        );
    }

    /**
     * Handle the LiveTvCategory "restored" event.
     */
    public function restored(LiveTvCategory $category): void
    {
        //
    }

    /**
     * Handle the LiveTvCategory "force deleted" event.
     */
    public function forceDeleted(LiveTvCategory $category): void
    {
        //
    }
}
