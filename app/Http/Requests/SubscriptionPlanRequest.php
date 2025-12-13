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
            'currency'       => ['required', 'string', 'in:SAR'],

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
            'is_customize'        => ['boolean'],

            'countryPrices' => ['nullable', 'array'],
            'countryPrices.*.price_currency' => ['required_if:is_customize,1', 'numeric', 'min:0'],
            'countryPrices.*.price_sar' => ['required_if:is_customize,1', 'numeric', 'min:0'],
            'countryPrices.*.currency' => ['required_if:is_customize,1', 'string'],
            'countryPrices.*.country_id' => ['required_if:is_customize,1', 'integer', 'exists:countries,id'],

            'planAccess'           => ['nullable', 'array'],
            'planAccess.*.id' => ['nullable', 'integer', 'exists:plan_content_access,id'],
            'planAccess.*.content_type' => ['sometimes', 'in:category,movie,series'],
            'planAccess.*.content_id' => ['sometimes', 'integer'],
            'planAccess.*.access_type' => ['sometimes', 'in:allow,deny'],
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
