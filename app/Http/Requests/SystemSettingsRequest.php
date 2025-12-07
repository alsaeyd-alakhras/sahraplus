<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemSettingsRequest extends FormRequest
{
   public function authorize(): bool
    {
        return true; // أو تحكم عبر Policy
    }

    public function rules(): array
    {
        return [
            // معلومات عامة
            'site_name_ar'        => ['required', 'string', 'max:150'],
            'site_name_en'        => ['required', 'string', 'max:150'],
            'site_description_ar' => ['nullable', 'string', 'max:500'],
            'site_description_en' => ['nullable', 'string', 'max:500'],
            'site_email'          => ['required', 'email', 'max:150'],
            'site_phone'          => ['nullable', 'string', 'max:30'],
            'site_address'        => ['nullable', 'string', 'max:255'],

            // واجهة
            'logo_url'       => ['nullable', 'string', 'max:255'], // قد يكون path داخلي أو رابط كامل
            'favicon_url'    => ['nullable', 'string', 'max:255'],
            'logoUpload'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp', 'max:4096'],
            'faviconUpload'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp,ico', 'max:2048'],
            'primary_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color'=> ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],

            // إعدادات افتراضية
            'default_country'  => ['nullable', 'string', 'size:2'],
            'default_currency' => ['nullable', 'string', 'size:3'],
            'timezone'         => ['nullable', 'string', 'max:100'],
            'default_language' => ['nullable', 'in:ar,en'],
            'max_login_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
            'user_registration'  => ['sometimes', 'boolean'],
            'email_verification' => ['sometimes', 'boolean'],

            // إعدادات المحتوى / البث
            'default_quality'   => ['nullable', 'in:auto,480,720,1080,4k'],
            'items_per_page'    => ['nullable', 'integer', 'min:10', 'max:100'],
            'auto_play'         => ['sometimes', 'boolean'],
            'enable_download'   => ['sometimes', 'boolean'],
            'enable_comments'   => ['sometimes', 'boolean'],
            'enable_ratings'    => ['sometimes', 'boolean'],
            'copyright_notice'  => ['nullable', 'string', 'max:500'],

            // وضع الصيانة
            'maintenance_mode'         => ['sometimes', 'boolean'],
            'maintenance_title'        => ['nullable', 'string', 'max:255'],
            'maintenance_message'      => ['nullable', 'string', 'max:500'],
            'maintenance_end_time'     => ['nullable', 'date'],
            'maintenance_contact_email'=> ['nullable', 'email', 'max:150'],
            'allow_admin_access'       => ['sometimes', 'boolean'],

            // اجتماعي
            'facebook_url'  => ['nullable', 'url', 'max:255'],
            'twitter_url'   => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'youtube_url'   => ['nullable', 'url', 'max:255'],
            'tiktok_url'    => ['nullable', 'url', 'max:255'],
            'telegram_url'  => ['nullable', 'url', 'max:255'],
            'whatsapp_url'  => ['nullable', 'url', 'max:255'],
            'snapchat_url'  => ['nullable', 'url', 'max:255'],

            // SEO
            'meta_title'          => ['nullable', 'string', 'max:255'],
            'meta_description'    => ['nullable', 'string', 'max:500'],
            'meta_keywords'       => ['nullable', 'string', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'google_search_console' => ['nullable', 'string', 'max:255'],

            // إعدادات تطبيق الهاتف
            'android_app_url'      => ['nullable', 'url', 'max:255'],
            'ios_app_url'          => ['nullable', 'url', 'max:255'],
            'huawei_app_url'       => ['nullable', 'url', 'max:255'],
            'app_version'          => ['nullable', 'string', 'max:50'],
            'min_supported_version'=> ['nullable', 'string', 'max:50'],
            'force_update'         => ['sometimes', 'boolean'],
            'mobile_update_message'=> ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'default_country'  => $this->default_country ? strtoupper(trim($this->default_country)) : $this->default_country,
            'default_currency' => $this->default_currency ? strtoupper(trim($this->default_currency)) : $this->default_currency,
        ]);
    }

    public function messages(): array
    {
        return [
            'site_name_ar.required' => 'اسم الموقع بالعربية مطلوب',
            'site_name_en.required' => 'اسم الموقع بالإنجليزية مطلوب',
            'site_email.required'   => 'البريد الإلكتروني مطلوب',
            'site_email.email'      => 'صيغة البريد الإلكتروني غير صحيحة',
            'default_country.size'  => 'رمز الدولة يجب أن يتكون من حرفين',
            'default_currency.size' => 'رمز العملة يجب أن يتكون من 3 أحرف',
        ];
    }
}
