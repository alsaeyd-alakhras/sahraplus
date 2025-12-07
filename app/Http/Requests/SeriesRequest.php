<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeriesRequest extends FormRequest
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
        $id = $this->route('series')?->id;

        return [
        'title_ar' => ['required','string','max:200'],
        'title_en' => ['nullable','string','max:200'],
        'description_ar' => ['nullable','string'],
        'description_en' => ['nullable','string'],
        'poster_url' => ['nullable','string','max:1000'],
        'poster_url_out' => ['nullable','string','max:1000'],
        'backdrop_url' => ['nullable','string','max:1000'],
        'backdrop_url_out' => ['nullable','string','max:1000'],
        'trailer_url' => ['nullable','url','max:1000'],
        'trailer_url_out' => ['nullable','string','max:1000'],
        'first_air_date' => ['nullable','date'],
        'last_air_date'  => ['nullable','date'],
        'seasons_count'  => ['nullable','integer','min:0'],
        'episodes_count' => ['nullable','integer','min:0'],
        'imdb_rating' => ['nullable','numeric','min:0','max:10'],
        'content_rating' => ['nullable','string','max:10'],
        'language' => ['required','string','max:5'],
        'country'  => ['nullable','string','size:2'],
        'status'   => ['required', Rule::in(['draft','published','archived'])],
        'series_status' => ['nullable', Rule::in(['returning','ended','canceled'])],
        'is_featured' => ['sometimes','boolean'],
        'is_kids' => ['sometimes','boolean'],
        'view_count'  => ['sometimes','integer','min:0'],
        'tmdb_id'     => ['nullable','string','max:20'],
        'created_by'  => ['nullable','exists:admins,id'],
         'logo_url'         => ['required'],

        // NEW: التصنيفات
        'category_ids'   => ['nullable','array'],
        'category_ids.*' => ['nullable','integer','exists:categories,id'],

        // NEW: الطاقم
        'cast'                 => ['nullable','array'],
        'cast.*.person_id'     => ['sometimes','distinct','exists:people,id'],
        'cast.*.role_type'     => ['nullable','string','max:100'],
        'cast.*.character_name'=> ['nullable','string','max:150'],
        'cast.*.sort_order'    => ['nullable','integer','min:0'],
    ];
    }
}
