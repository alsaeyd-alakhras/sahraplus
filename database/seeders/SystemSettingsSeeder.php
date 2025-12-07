<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Seed the system_settings table with default values.
     */
    public function run(): void
    {
        $settings = [
            // معلومات عامة
            'site_name_ar'        => 'سهرة بلس',
            'site_name_en'        => 'Sahra Plus',
            'site_description_ar' => 'منصة ترفيهية لمشاهدة أحدث الأفلام والمسلسلات بجودة عالية.',
            'site_description_en' => 'Entertainment platform to watch the latest movies and series in high quality.',
            'site_email'          => 'contact@sahra.com',
            'site_phone'          => '966501234567+',
            'site_address'        => 'Riyadh, Saudi Arabia',

            // الهوية البصرية / الواجهة
            'logo_url'        => '',
            'favicon_url'     => '',
            'primary_color'   => '#6f42c1',
            'secondary_color' => '#6c757d',
            'background_color'=> '#ffffff',

            // إعدادات المحتوى / العرض
            'default_quality'  => 'auto',
            'items_per_page'   => '20',
            'auto_play'        => '0',
            'enable_download'  => '0',
            'enable_comments'  => '1',
            'enable_ratings'   => '1',
            'copyright_notice' => '© ' . date('Y') . ' Sahra Plus. All rights reserved.',

            // SEO
            'meta_title'          => 'سهرة بلس - منصة الترفيه المفضلة لديك',
            'meta_description'    => 'منصة سهرة بلس لمشاهدة أحدث الأفلام والمسلسلات والبرامج بجودة عالية.',
            'meta_keywords'       => 'سهرة بلس, أفلام, مسلسلات, بث مباشر',
            'google_analytics_id' => '',
            'google_search_console' => '',

            // إعدادات النظام
            'default_country'     => 'SA',
            'default_currency'    => 'SAR',
            'timezone'            => 'Asia/Riyadh',
            'default_language'    => 'ar',
            'max_login_attempts'  => '5',
            'user_registration'   => '1',
            'email_verification'  => '0',

            // تواصل اجتماعي
            'facebook_url'  => '',
            'twitter_url'   => '',
            'instagram_url' => '',
            'youtube_url'   => '',
            'tiktok_url'    => '',
            'telegram_url'  => '',
            'whatsapp_url'  => '',
            'snapchat_url'  => '',

            // وضع الصيانة
            'maintenance_mode'          => '0',
            'maintenance_title'         => 'الموقع تحت الصيانة حالياً',
            'maintenance_message'       => 'نقوم ببعض التحسينات على المنصة وسنعود للعمل في أقرب وقت ممكن.',
            'maintenance_end_time'      => '',
            'maintenance_contact_email' => 'contact@sahra.com',
            'allow_admin_access'        => '1',

            // إعدادات تطبيق الهاتف
            'android_app_url'       => '',
            'ios_app_url'           => '',
            'huawei_app_url'        => '',
            'app_version'           => '1.0.0',
            'min_supported_version' => '1.0.0',
            'force_update'          => '0',
            'mobile_update_message' => 'يرجى تحديث التطبيق للحصول على أحدث المزايا والتحسينات.',

            // إعدادات الضريبة
            'tax' => '0',
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }
    }
}


