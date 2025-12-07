<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemSettingsResource;
use App\Services\SystemSettingService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    protected SystemSettingService $service;

    public function __construct(SystemSettingService $service)
    {
        $this->service = $service;
    }
    public function edit()
    {
        $data = $this->service->getFormData();

        // تجهيز روابط الشعار والأيقونة بشكل محترم:
        // - لو القيمة path داخل storage نحولها إلى رابط كامل
        // - لو القيمة رابط كامل نتركها كما هي
        // - لو فاضية نستخدم القيم الافتراضية من config/settings.php
        $logo = $data['logo_url'] ?? '';
        if ($logo) {
            if (! Str::startsWith($logo, ['http://', 'https://'])) {
                $data['logo_url'] = asset('storage/' . ltrim($logo, '/'));
            }
        } elseif (config('settings.app_logo')) {
            $data['logo_url'] = asset(config('settings.app_logo'));
        }

        $favicon = $data['favicon_url'] ?? '';
        if ($favicon) {
            if (! Str::startsWith($favicon, ['http://', 'https://'])) {
                $data['favicon_url'] = asset('storage/' . ltrim($favicon, '/'));
            }
        } elseif (config('settings.app_icon')) {
            $data['favicon_url'] = asset(config('settings.app_icon'));
        }

        // رقم بسيط لإصدار إعدادات الـ API (يساعد مطوّر التطبيق إن احتاج)
        $data['api_settings_version'] = 1;

        return new SystemSettingsResource($data);
    }
}
