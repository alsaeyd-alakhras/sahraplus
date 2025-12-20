<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class HomeSectionRequest extends FormRequest
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
        $id = $this->route('home_section');

        return [
            'title_ar'        => ['required', 'string', 'max:150'],
            'title_en'        => ['nullable', 'string', 'max:150'],

            'platform'        => ['required', 'in:mobile,web,both'],
            'is_kids'         => ['boolean'],
            'is_active'       => ['boolean'],

            'sort_order'     => ['nullable', 'integer', 'min:0'],

            'starts_at'      => ['nullable', 'date'],
            'ends_at'        => ['nullable', 'date', 'after_or_equal:starts_at'],

            'sectionItems'           => ['nullable', 'array'],
            'sectionItems.*.id' => ['nullable', 'integer', 'exists:home_section_items,id'],
            'sectionItems.*.content_type' => ['sometimes', 'in:movie,series'],
            'sectionItems.*.content_id' => ['sometimes', 'integer'],
            'sectionItems.*.sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_kids'   => $this->boolean('is_kids'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}

