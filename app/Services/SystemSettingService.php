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
        'site_description_ar',
        'site_description_en',
        'site_email',
        'site_phone',
        'site_address',
        'site_tax',
        // الهوية البصرية / الواجهة        'logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'background_color',

        // إعدادات المحتوى / العرض
        'default_quality',
        'items_per_page',
        'auto_play',
        'enable_download',
        'enable_comments',
        'enable_ratings',
        'copyright_notice',

        // إعدادات SEO
        'meta_title',
        'meta_description',
        'meta_keywords',
        'google_analytics_id',
        'google_search_console',

        // إعدادات النظام
        'default_country',
        'default_currency',
        'timezone',
        'default_language',
        'max_login_attempts',
        'user_registration',
        'email_verification',

        // تواصل اجتماعي
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'telegram_url',
        'whatsapp_url',
        'snapchat_url',

        // وضع الصيانة
        'maintenance_mode',
        'maintenance_title',
        'maintenance_message',
        'maintenance_end_time',
        'maintenance_contact_email',
        'allow_admin_access',

        // إعدادات تطبيق الهاتف
        'android_app_url',
        'ios_app_url',
        'huawei_app_url',
        'app_version',
        'min_supported_version',
        'force_update',
        'mobile_update_message',
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

        // ضبط قيم منطقية بقيم افتراضية منطقية
        $boolDefaults = [
            'maintenance_mode'   => false,
            'auto_play'          => false,
            'enable_download'    => false,
            'enable_comments'    => true,
            'enable_ratings'     => true,
            'user_registration'  => true,
            'email_verification' => false,
            'allow_admin_access' => true,
            'force_update'       => false,
        ];

        foreach ($boolDefaults as $key => $default) {
            $value = $current[$key] ?? null;
            $data[$key] = $value === null ? $default : (bool) $value;
        }

        // ضبط نصوص افتراضية
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

            // تحويل الحقول المنطقية إلى 0/1
            $booleanKeys = [
                'maintenance_mode',
                'auto_play',
                'enable_download',
                'enable_comments',
                'enable_ratings',
                'user_registration',
                'email_verification',
                'allow_admin_access',
                'force_update',
            ];

            foreach ($booleanKeys as $key) {
                $data[$key] = isset($data[$key]) && (int) $data[$key] === 1 ? '1' : '0';
            }

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
