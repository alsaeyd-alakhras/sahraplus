<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Watchlist;
use Carbon\Carbon;

class DeleteOldWatchlists extends Command
{
    /**
     * اسم الـ command
     */
    protected $signature = 'watchlists:cleanup';

    /**
     * وصف الـ command
     */
    protected $description = 'Delete watchlist records older than 90 days';

    /**
     * تنفيذ الـ command
     */
    public function handle()
    {
        $dateThreshold = Carbon::now()->subDays(90);

        $deleted = Watchlist::where('created_at', '<', $dateThreshold)->forceDelete();

        $this->info("Deleted {$deleted} old watchlist records.");
        return Command::SUCCESS;
    }
}