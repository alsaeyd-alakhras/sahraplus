<?php

namespace App\Services;

use App\Repositories\SystemSettingRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SystemSettingService
{
    public const DEFAULT_KEYS = [
        // معلومات عامة
        'site_name_ar',
        'site_name_en',
        'site_email',
        'site_phone',
        'site_address',
        'site_tax',
        // واجهة
        'logo_url',
        'favicon_url',
        // إعدادات افتراضية
        'default_country',
        'default_currency',
        'timezone',
        // وضع الصيانة
        'maintenance_mode',
        'maintenance_message',
        // اجتماعي
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
    ];

    protected SystemSettingRepository $repo;

    public function __construct(SystemSettingRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getFormData(): array
    {
        // كل ما هو مخزّن فعليًا بالداتابيس: ['key' => 'value', ...]
        $current = $this->repo->getAllKeyed(); // pluck('value','key')

        // مفاتيح افتراضية بقيم فاضية
        $defaults = array_fill_keys(self::DEFAULT_KEYS, '');

        // دمج الموجود من الداتابيس فوق الافتراضي
        $data = array_merge($defaults, $current);

        // ضبط قيم منطقية/نصوص
        $data['maintenance_mode']    = (bool)($current['maintenance_mode'] ?? false);
        $data['maintenance_message'] = $data['maintenance_message'] ?? '';

        return $data;
    }

    public function update(array $data): void
    {
        DB::beginTransaction();
        try {
            // رفع الشعارات (اختياري): logoUpload / faviconUpload
            if (isset($data['logoUpload']) && $data['logoUpload'] instanceof UploadedFile) {
                $path = $data['logoUpload']->store('branding', 'public');
                $data['logo_url'] = $path;
            }
            if (isset($data['faviconUpload']) && $data['faviconUpload'] instanceof UploadedFile) {
                $path = $data['faviconUpload']->store('branding', 'public');
                $data['favicon_url'] = $path;
            }

            // تحويل Boolean
            $data['maintenance_mode'] = isset($data['maintenance_mode']) && (int)$data['maintenance_mode'] === 1 ? '1' : '0';

            // اقتطاف المفاتيح المسموحة فقط
            $allowed = array_intersect_key($data, array_flip(self::DEFAULT_KEYS));

            $this->repo->setMany($allowed);

            // (اختياري) Activity Log
            // ActivityLogService::log('Updated', 'SystemSettings', 'تم تعديل إعدادات النظام.', null, $allowed);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // throw $e;
            Log::channel('system')->error($e);
            abort(500, 'حدث خطأ أثناء تحديث الإعدادات.');
        }
    }
}
