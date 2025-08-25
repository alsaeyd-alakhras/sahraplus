<?php

namespace App\Observers;

use App\Models\Country;
use App\Services\ActivityLogService;

class CountryObserver
{
    /**
     * Handle the Country "created" event.
     */
    public function created(Country $country): void
    {
         ActivityLogService::log(
            'Created',
            'Country',
            "تم إضافة الدولة: {$country->name_ar} ({$country->code}).",
            null,
            $country->toArray()
        );
    }

    /**
     * Handle the Country "updated" event.
     */
    public function updated(Country $country): void
    {
        //
    }

    /**
     * Handle the Country "deleted" event.
     */
    public function deleted(Country $country): void
    {
         ActivityLogService::log(
            'Deleted',
            'Country',
            "تم حذف الدولة: {$country->name_ar} ({$country->code}).",
            $country->toArray(),
            null
        );
    }

    /**
     * Handle the Country "restored" event.
     */
    public function restored(Country $country): void
    {
        //
    }

    /**
     * Handle the Country "force deleted" event.
     */
    public function forceDeleted(Country $country): void
    {
        //
    }
}
