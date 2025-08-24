<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->route('country');
    }

    protected function prepareForValidation(): void
    {
        // تنسيقات قبل التحقق
        $this->merge([
            'code'      => isset($this->code) ? strtoupper(trim($this->code)) : $this->code,
            'currency'  => isset($this->currency) ? strtoupper(trim($this->currency)) : $this->currency,
            'name_ar'   => isset($this->name_ar) ? trim($this->name_ar) : $this->name_ar,
            'name_en'   => isset($this->name_en) ? trim($this->name_en) : $this->name_en,
            'dial_code' => isset($this->dial_code) ? trim($this->dial_code) : $this->dial_code,
        ]);
    }

    public function rules(): array
    {
        $countryId = $this->route('country')?->id;

        return [
            'code' => [
                'required',
                'string',
                'size:2',
                // لو بدك تجبرها حروف لاتينية فقط، فعّل السطر التالي:
                // 'regex:/^[A-Z]{2}$/',
                Rule::unique('countries', 'code')->ignore($countryId),
            ],
            'name_ar'   => ['required', 'string', 'max:100'],
            'name_en'   => ['required', 'string', 'max:100'],
            'dial_code' => [
                'required',
                'string',
                'max:10',
                // مثال لصيغة رمز الاتصال (+966، 02، 1...). عطّلها إذا ما بدك تقييد:
                // 'regex:/^\+?\d{1,9}$/',
            ],
            'currency'  => [
                'required',
                'string',
                'size:3',
                // لو حاب تتأكد أنها حروف فقط:
                // 'regex:/^[A-Z]{3}$/',
            ],
            'flag_url'  => ['nullable', 'string', 'max:255'], // أو 'url' إذا دائماً رابط كامل
            'is_active' => ['sometimes', 'boolean'],
            'sort_order'=> ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'رمز الدولة مطلوب',
            'code.size'     => 'رمز الدولة يجب أن يتكون من حرفين',
            'code.unique'   => 'رمز الدولة مستخدم مسبقًا',

            'name_ar.required' => 'اسم الدولة بالعربية مطلوب',
            'name_ar.max'      => 'اسم الدولة بالعربية يجب ألا يزيد عن 100 حرف',

            'name_en.required' => 'اسم الدولة بالإنجليزية مطلوب',
            'name_en.max'      => 'اسم الدولة بالإنجليزية يجب ألا يزيد عن 100 حرف',

            'dial_code.required' => 'رمز الاتصال مطلوب',
            'dial_code.max'      => 'رمز الاتصال يجب ألا يزيد عن 10 أحرف',
            // 'dial_code.regex'  => 'صيغة رمز الاتصال غير صحيحة',

            'currency.required' => 'رمز العملة مطلوب',
            'currency.size'     => 'رمز العملة يجب أن يتكون من 3 أحرف',
            // 'currency.regex'   => 'رمز العملة يجب أن يكون أحرفًا لاتينية فقط',

            // 'flag_url.url'    => 'رابط العلم غير صالح',
            'flag_url.max'      => 'رابط العلم طويل أكثر من اللازم',

            'is_active.boolean' => 'حقل الحالة يجب أن يكون صحيح/خطأ',
            'sort_order.integer'=> 'ترتيب العرض يجب أن يكون رقمًا صحيحًا',
            'sort_order.min'    => 'ترتيب العرض يجب ألا يكون سالبًا',
        ];
    }
}
