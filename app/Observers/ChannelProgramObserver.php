<?php

namespace App\Observers;

use App\Models\ChannelProgram;
use App\Services\ActivityLogService;

class ChannelProgramObserver
{
    public function created(ChannelProgram $program): void
    {
        ActivityLogService::log(
            'Created',
            'ChannelProgram',
            "تم إضافة برنامج جديد: {$program->title_ar} على قناة {$program->channel?->name_ar}.",
            null,
            $program->toArray()
        );
    }

    public function deleted(ChannelProgram $program): void
    {
        ActivityLogService::log(
            'Deleted',
            'ChannelProgram',
            "تم حذف برنامج: {$program->title_ar}.",
            $program->toArray(),
            null
        );
    }
}
