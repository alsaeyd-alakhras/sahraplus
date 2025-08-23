<?php

namespace App\Observers;

use App\Models\Admin;
use App\Services\ActivityLogService;

class AdminObserver
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Admin $admin): void
    {
        ActivityLogService::log(
            'Created',
            'Admin',
            "تم إضافة الموظف / الأدمن : {$admin->name}.",
            null,
            $admin->toArray()
        );
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Admin $admin): void
    {
        //
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Admin $admin): void
    {
        ActivityLogService::log(
            'Deleted',
            'Admin',
            "تم حذف الموظف / الأدمن : {$admin->name}.",
            $admin->toArray(),
            null
        );
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Admin $admin): void
    {
        //
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Admin $admin): void
    {
        //
    }
}
