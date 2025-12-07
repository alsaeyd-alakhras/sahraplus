<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPlanRequest extends FormRequest
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
        $id = $this->route('sub_plans');

        return [
            'name_ar'        => ['required', 'string', 'max:100'],
            'name_en'        => ['required', 'string', 'max:100'],

            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'price'          => ['required', 'numeric', 'min:2'],
            'currency'       => ['required', 'string', 'max:3'],

            'billing_period' => ['required', 'in:monthly,quarterly,yearly'],
            'video_quality'  => ['required', 'in:sd,hd,uhd'],

            'trial_days'     => ['nullable', 'integer', 'min:0'],
            'max_profiles'   => ['nullable', 'integer', 'min:0'],
            'max_devices'    => ['nullable', 'integer', 'min:0'],

            'sort_order'     => ['nullable', 'integer', 'min:0'],

            'features'       => ['nullable', 'array'],

            'download_enabled' => ['boolean'],
            'ads_enabled'      => ['boolean'],
            'live_tv_enabled'  => ['boolean'],
            'is_popular'       => ['boolean'],
            'is_active'        => ['boolean'],

            'cast'           => ['nullable', 'array'],

            // نوع التقييد (string max 50)
            'cast.*.limitation_type' => ['required', 'string', 'max:50'],
            // مفتاح التقييد يجب أن يكون فريد لكل plan_id
            'cast.*.limitation_key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('plan_limitations')
                    ->where(fn($query) => $query->where('plan_id', $this->plan_id))
                    ->ignore($id),
            ],
            // القيمة (string max 100)
            'cast.*.limitation_value' => ['required', 'string', 'max:100'],

            // الوحدة اختيارية
            'cast.*.limitation_unit' => ['nullable', 'string', 'max:20'],

            // الوصف العربي والإنجليزي اختياري
            'cast.*.description_ar' => ['nullable', 'string'],
            'cast.*.description_en' => ['nullable', 'string'],

        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'download_enabled' => $this->boolean('download_enabled'),
            'ads_enabled'      => $this->boolean('ads_enabled'),
            'live_tv_enabled'  => $this->boolean('live_tv_enabled'),
            'is_popular'       => $this->boolean('is_popular'),
            'is_active'        => $this->boolean('is_active'),
        ]);
    }
}