<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovieRequest extends FormRequest
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
        $id = $this->route('movie')?->id;

        return [
            'title_ar'         => ['required','string'],
            'title_en'         => ['nullable','string'],
            'description_ar'   => ['nullable','string'],
            'description_en'   => ['nullable','string'],
            'poster_url'       => ['nullable','string','max:1000'],
            'backdrop_url'     => ['nullable','string','max:1000'],
            'trailer_url'      => ['nullable','url','max:1000'],
            'release_date'     => ['nullable','date'],
            'duration_minutes' => ['nullable','integer','min:0'],
            'imdb_rating'      => ['nullable','numeric','min:0','max:10'],
            'content_rating'   => ['nullable','string','max:10'],
            'language'         => ['required','string','max:5'],
            'country'          => ['nullable','string','size:2'],
            'status'           => ['required', Rule::in(['draft','published','archived'])],
            'is_featured'      => ['sometimes','boolean'],
            'view_count'       => ['sometimes','integer','min:0'],
            'tmdb_id'          => ['nullable','string','max:20'],
        ];
    }
}
