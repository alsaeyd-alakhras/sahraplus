<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SystemSetting;

class CurrencyService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.currencyfreaks.key');
    }

    /**
     * جلب أسعار الصرف وتحويلها لصيغة: 1 SAR = X currency
     */
    public function updateRates(): bool
    {
        $url = "https://api.currencyfreaks.com/v2.0/rates/latest?apikey={$this->apiKey}";
        $response = Http::get($url);

        if (!$response->ok()) {
            return false;
        }

        $data  = $response->json();
        $rates = $data['rates'] ?? [];

        // القيم أساسها USD — بدنا نحولها لأساس SAR
        if (!isset($rates['SAR'])) {
            return false;
        }

        $usdToSar = floatval($rates['SAR']); // كم SAR = 1 USD

        $ratesPerSar = [];

        foreach ($rates as $currency => $valuePerUsd) {
            $valuePerUsd = floatval($valuePerUsd);
            if ($valuePerUsd == 0) {
                $ratesPerSar[$currency] = 0;
                continue;
            }

            // 1 SAR = كم من العملة؟
            $ratesPerSar[$currency] = $valuePerUsd / $usdToSar;
        }

        // خزّن القيم بالشكل الصحيح
        SystemSetting::updateOrCreate(
            ['key' => 'currency_rates'],
            ['value' => json_encode($ratesPerSar)]
        );

        return true;
    }

    /**
     * جلب سعر عملة محددة مقابل الريال (صيغة per SAR)
     */
    public function getRate(string $currency): float
    {
        $setting = SystemSetting::where('key', 'currency_rates')->first();
        $rates   = $setting ? json_decode($setting->value, true) : [];

        return floatval($rates[$currency] ?? 0);
    }
}
