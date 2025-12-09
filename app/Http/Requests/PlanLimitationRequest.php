<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PlanLimitationRequest extends FormRequest
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
        // ID الخاص بالـ limitation لو كان في التحديث
        $id = $this->route('sub_plans');

        return [
            'plan_id' => ['required', 'exists:subscription_plans,id'],

            // نوع التقييد (string max 50)
            'limitation_type' => ['required', 'string', 'max:50'],

            // مفتاح التقييد يجب أن يكون فريد لكل plan_id
            'limitation_key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('plan_limitations')
                    ->where(fn($query) => $query->where('plan_id', $this->plan_id))
                    ->ignore($id),
            ],

            // القيمة (string max 100)
            'limitation_value' => ['required', 'string', 'max:100'],

            // الوحدة اختيارية
            'limitation_unit' => ['nullable', 'string', 'max:20'],

            // الوصف العربي والإنجليزي اختياري
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            // حالة التفعيل اختيارية
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

}
