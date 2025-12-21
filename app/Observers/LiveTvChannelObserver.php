<?php

namespace App\Observers;

use App\Models\LiveTvChannel;
use App\Services\ActivityLogService;

class LiveTvChannelObserver
{
    public function created(LiveTvChannel $channel): void
    {
        $data = $channel->toArray();
        unset($data['category']);

        ActivityLogService::log(
            'Created',
            'LiveTvChannel',
            'تم إضافة قناة: ' . $channel->name_ar . ' - الفئة: ' . ($channel->category?->name_ar ?? '-'),
            null,
            $data
        );
    }

    public function deleted(LiveTvChannel $channel): void
    {
        $data = $channel->toArray();
        unset($data['category']);

        ActivityLogService::log(
            'Deleted',
            'LiveTvChannel',
            'تم حذف قناة: ' . $channel->name_ar,
            $data,
            null
        );
    }
}
