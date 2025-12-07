<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // اسم الباراميتر لازم يكون مطابق للـ route
        $id = $this->route('tax');

        return [
            'name_ar' => ['required', 'string', 'max:100'],
            'name_en' => ['required', 'string', 'max:100'],

            'tax_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('taxes', 'tax_code')->ignore($id),
            ],

            'tax_type' => ['required', Rule::in(['fixed', 'percentage'])],

            'tax_rate' => ['required', 'numeric', 'min:0'],

            // 'applicable_countries' => ['nullable', 'array'],
            'applicable_countries.*' => ['string'],

            //'applicable_plans' => ['nullable', 'array'],
            'applicable_plans.*' => ['string'],

            'min_amount' => ['required', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'gte:min_amount'],

            'is_active' => ['sometimes', 'boolean'],
            'compound_tax' => ['sometimes', 'boolean'],

            'effective_from' => ['required', 'date'],
            'effective_until' => [
                'nullable',
                'date',
                //'after_or_equal:effective_from'
            ],
        ];
    }
}
