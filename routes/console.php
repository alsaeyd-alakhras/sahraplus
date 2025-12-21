<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\DeleteExpiredDownloads;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\UpdateViewingStats;
use App\Jobs\ProcessUserRating;
use App\Jobs\CheckStreamHealth;
use App\Jobs\SyncEPGData;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::job(new DeleteExpiredDownloads)->dailyAt('02:00');
Schedule::job(new UpdateViewingStats)->everyFifteenMinutes();
Schedule::job(new ProcessUserRating)->hourly();
Schedule::job(new CheckStreamHealth)->everyFiveMinutes();
Schedule::job(new SyncEPGData)->dailyAt('03:00');
// Clean old EPG data weekly (Monday at 3 AM)
Schedule::call(function () {
    $epgService = app(\App\Services\EPGService::class);
    $epgService->cleanOldPrograms(7);
})->weeklyOn(1, '03:00');
