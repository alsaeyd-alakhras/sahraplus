<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\DeleteExpiredDownloads;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\UpdateViewingStats;
use App\Jobs\ProcessUserRating;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::job(new DeleteExpiredDownloads)->dailyAt('02:00');
Schedule::job(new UpdateViewingStats)->everyFifteenMinutes();
Schedule::job(new ProcessUserRating)->hourly();
