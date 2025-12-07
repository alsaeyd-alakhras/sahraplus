<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PlanContentAccessRequest extends FormRequest
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
        $id = $this->route('plan_access'); // تغيير اسم الـ route parameter حسب الـ route الفعلي

        return [
            'plan_id' => ['required', 'exists:subscription_plans,id'],

            'content_type' => ['required', Rule::in(['category', 'movie', 'series'])],

            'content_id' => ['required', 'integer'],

            'access_type' => ['required', Rule::in(['allow', 'deny'])],
        ];
    }

}
