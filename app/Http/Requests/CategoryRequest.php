<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $id = $this->route('movie_category')?->id;
        return [
            'name_ar' => ['required','string','max:100'],
            'name_en' => ['required','string','max:100'],
            'description_ar' => ['nullable','string'],
            'description_en' => ['nullable','string'],
            'image_url' => ['nullable','string','max:2048'],
            'color'     => ['nullable','regex:/^#?[0-9A-Fa-f]{6}$/'],
            'sort_order'=> ['nullable','integer','min:0'],
            'is_active' => ['sometimes','boolean'],
        ];
    }

}
