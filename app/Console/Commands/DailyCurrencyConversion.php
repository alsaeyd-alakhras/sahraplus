<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyCurrencyConversion extends Command
{
    protected $signature = 'currency:convert-daily';

    protected $description = 'Convert all SAR prices to currency daily';

    public function handle()
    {
        $ser = new CurrencyService();
        $ser->updateRates();
        $this->info('Daily currency conversion completed.');
    }
}