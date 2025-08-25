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
            'site_name_ar' => ['required','string','max:150'],
            'site_name_en' => ['required','string','max:150'],
            'site_email'   => ['required','email','max:150'],
            'site_phone'   => ['nullable','string','max:30'],
            'site_address' => ['nullable','string','max:255'],

            // واجهة
            'logo_url'    => ['nullable','string','max:255'], // أو 'url'
            'favicon_url' => ['nullable','string','max:255'], // أو 'url'
            'logoUpload'  => ['nullable','file','mimes:jpg,jpeg,png,svg,webp','max:4096'],
            'faviconUpload' => ['nullable','file','mimes:jpg,jpeg,png,svg,webp,ico','max:2048'],

            // إعدادات افتراضية
            'default_country'  => ['nullable','string','size:2'],
            'default_currency' => ['nullable','string','size:3'],
            'timezone'         => ['nullable','string','max:100'],

            // وضع الصيانة
            'maintenance_mode'    => ['sometimes','boolean'],
            'maintenance_message' => ['nullable','string','max:500'],

            // اجتماعي
            'facebook_url'  => ['nullable','url','max:255'],
            'twitter_url'   => ['nullable','url','max:255'],
            'instagram_url' => ['nullable','url','max:255'],
            'youtube_url'   => ['nullable','url','max:255'],
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
