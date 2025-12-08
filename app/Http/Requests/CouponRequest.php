<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
        $id = $this->route('coupons'); // تغيير اسم الـ route parameter حسب الـ route الفعلي

        return [
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'discount_type' => ['required', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['required', 'numeric'],
            'starts_at' => ['nullable', 'date'],
            'code' => ['required', 'string'],
            'expires_at' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer'],
            'times_used' => ['required', 'integer'],
            'coupon_info' => ['nullable', 'string'],
            'usage_limit_per_user' => ['required', 'integer'],

        ];
    }
}
