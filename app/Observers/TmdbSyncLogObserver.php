<?php

namespace App\Observers;

use App\Models\TmdbSyncLog;
use App\Services\ActivityLogService;

class TmdbSyncLogObserver
{
    public function created(TmdbSyncLog $log): void
    {
        ActivityLogService::log(
            'Created',
            'TmdbSyncLog',
            "تم إنشاء سجل TMDB للنوع {$log->content_type} (TMDB: {$log->tmdb_id}) بحالة {$log->status}.",
            null,
            $log->toArray()
        );
    }

    /**
     * Handle the TmdbSyncLog "updated" event.
     */
    public function updated(TmdbSyncLog $log): void
    {
        //
    }

    /**
     * Handle the TmdbSyncLog "deleted" event.
     */
    public function deleted(TmdbSyncLog $log): void
    {
        ActivityLogService::log(
            'Deleted',
            'TmdbSyncLog',
            "تم حذف سجل TMDB (TMDB: {$log->tmdb_id}).",
            $log->toArray(),
            null
        );
    }

    /**
     * Handle the TmdbSyncLog "restored" event.
     */
    public function restored(TmdbSyncLog $log): void
    {
        //
    }

    /**
     * Handle the TmdbSyncLog "force deleted" event.
     */
    public function forceDeleted(TmdbSyncLog $log): void
    {
        //
    }
}
