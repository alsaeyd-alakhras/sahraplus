<?php

namespace App\Observers;

use App\Models\Notification;
use App\Services\ActivityLogService;

class NotificationObserver
{
    public function created(Notification $notification): void
    {
        ActivityLogService::log(
            'Created',
            'Notification',
            "تم إنشاء إشعار جديد من النوع {$notification->type}.",
            null,
            $notification->toArray()
        );
    }

    /**
     * Handle the Notification "updated" event.
     */
    public function updated(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "deleted" event.
     */
    public function deleted(Notification $notification): void
    {
        ActivityLogService::log(
            'Deleted',
            'Notification',
            "تم حذف إشعار من النوع {$notification->type}.",
            $notification->toArray(),
            null
        );
    }

    /**
     * Handle the Notification "restored" event.
     */
    public function restored(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     */
    public function forceDeleted(Notification $notification): void
    {
        //
    }
}
