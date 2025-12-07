<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeBannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content_type' => ['required', Rule::in(['movie', 'series'])],
            'content_id' => ['required', 'integer'],
            'placement' => ['required', Rule::in(['frontend_slider', 'mobile_banner'])],
            'is_kids' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    /**
     * Custom validation after standard rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $contentType = $this->input('content_type');
            $contentId = $this->input('content_id');

            if ($contentType && $contentId) {
                $table = $contentType === 'movie' ? 'movies' : 'series';
                $exists = \DB::table($table)->where('id', $contentId)->exists();
                
                if (!$exists) {
                    $validator->errors()->add('content_id', "المحتوى المحدد غير موجود في جدول {$table}");
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content_type.required' => 'نوع المحتوى مطلوب',
            'content_type.in' => 'نوع المحتوى يجب أن يكون فيلم أو مسلسل',
            'content_id.required' => 'معرف المحتوى مطلوب',
            'content_id.integer' => 'معرف المحتوى يجب أن يكون رقماً',
            'placement.required' => 'مكان العرض مطلوب',
            'placement.in' => 'مكان العرض يجب أن يكون سلايدر الموقع أو بانر الجوال',
            'sort_order.integer' => 'ترتيب العرض يجب أن يكون رقماً',
            'sort_order.min' => 'ترتيب العرض يجب أن يكون صفر أو أكبر',
            'ends_at.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ];
    }
}

