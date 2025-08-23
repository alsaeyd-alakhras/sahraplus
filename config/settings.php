<?php

return [

    // معلومات عامة عن الموقع أو النظام
    'app_name' => "منصة سهرة",
    'app_description' => 'منصة محتوى ومشاهدة أفلام ومسلسلات عالمية وعربية',
    'app_logo' => '/imgs/logo-brand.png',
    'app_icon' => '/imgs/icon.png',
    'default_language' => 'ar',
    'fallback_language' => 'ar',

    // إعدادات العملة
    'default_currency' => 'USD',
    'currency_symbol' => '$',
    'currency_precision' => 2,

    // الإعدادات الخاصة بالمستخدم
    'max_login_attempts' => 5,
    'lockout_time' => 10, // بالدقائق

    // إعدادات واجهة المستخدم
    'pagination_limit' => 20,
    'date_format' => 'Y-m-d',
    'datetime_format' => 'Y-m-d H:i',

    // ملفات مرفقة
    'allowed_file_types' => ['pdf', 'docx', 'xlsx', 'jpg', 'png'],

    // روابط مهمة
    'support_email' => 'alsaeydjalakhras@gmail.com',
    'whatsapp_support' => 'https://wa.me/+972594318545',

    // كاش افتراضي
    'cache_duration' => 60 * 60, // بالثواني (1 ساعة)

];
