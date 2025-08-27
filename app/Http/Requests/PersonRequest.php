<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->route('person');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nationality' => $this->nationality ? trim($this->nationality) : $this->nationality,
            'gender'      => $this->gender ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name_ar'      => ['required','string','max:100'],
            'name_en'      => ['nullable','string','max:100'],
            'bio_ar'       => ['nullable','string'],
            'bio_en'       => ['nullable','string'],
            'photo_url'    => ['nullable','string','max:1000'],
            'birth_date'   => ['nullable','date'],
            'birth_place'  => ['nullable','string','max:100'],
            'nationality'  => ['nullable','string','max:50'],
            'gender'       => ['nullable', Rule::in(['male','female'])],
            'known_for'    => ['nullable'], // مصفوفة أو نص مفصول بفواصل
            'tmdb_id'      => ['nullable','string','max:20'],
            'is_active'    => ['sometimes','boolean'],

            // رفع صورة اختياري
            'photoUpload'  => ['nullable','file','mimes:jpg,jpeg,png,webp','max:8192'],
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => 'الاسم بالعربية مطلوب',
        ];
    }
}
