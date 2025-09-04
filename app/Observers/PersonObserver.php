<?php

namespace App\Observers;

use App\Models\Person;
use App\Services\ActivityLogService;


class PersonObserver
{
    public function created(Person $person): void
    {
        ActivityLogService::log(
            'Created',
            'Person',
            "تمت إضافة الشخص: {$person->name_ar}.",
            null,
            $person->toArray()
        );
    }

    /**
     * Handle the Person "updated" event.
     */
    public function updated(Person $person): void
    {
        //
    }

    /**
     * Handle the Person "deleted" event.
     */
    public function deleted(Person $person): void
    {
        ActivityLogService::log(
            'Deleted',
            'Person',
            "تم حذف الشخص: {$person->name_ar}.",
            $person->toArray(),
            null
        );
    }

    /**
     * Handle the Person "restored" event.
     */
    public function restored(Person $person): void
    {
        //
    }

    /**
     * Handle the Person "force deleted" event.
     */
    public function forceDeleted(Person $person): void
    {
        //
    }
}
