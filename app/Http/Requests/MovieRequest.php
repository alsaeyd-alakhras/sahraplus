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

    protected function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH');
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
            'title_ar'         => ['required','string','max:255'],
            'title_en'         => ['nullable','string','max:255'],
            'description_ar'   => ['nullable','string'],
            'description_en'   => ['nullable','string'],
            'poster_url'       => ['nullable','string','max:1000'],
            'poster_url_out'   => ['nullable','string','max:1000'],
            'backdrop_url'     => ['nullable','string','max:1000'],
            'backdrop_url_out' => ['nullable','string','max:1000'],
            'trailer_url'      => ['nullable','url','max:1000'],
            'trailer_url_out'  => ['nullable','string','max:1000'],
            'release_date'     => ['nullable','date'],
            'duration_minutes' => ['nullable','integer','min:0'],
            'imdb_rating'      => ['nullable','numeric','min:0','max:10'],
            'content_rating'   => ['nullable','string','max:10'],
            'language'         => ['required','string','max:5'],
            'country'          => ['nullable','string','size:2'],
            'status'           => ['required', Rule::in(['draft','published','archived'])],
            'is_featured'      => ['sometimes','boolean'],
            'is_kids'          => ['sometimes','boolean'],
            // الأفضل ما يجي من الفورم بل من الكود
            'view_count'       => ['nullable','integer','min:0'],
            'tmdb_id'          => ['nullable','string','max:20'],
             'intro_skip_time'         => ['required'],
             'logo_url'         => ['required'],
             
            // التصنيفات
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['nullable', 'integer', 'exists:categories,id'],

            // الطاقم
            'cast'           => ['nullable', 'array'],
            'cast.*.person_id' => ['sometimes', 'distinct', 'exists:people,id'],
            'cast.*.role_type'           => ['nullable', 'string', 'max:100'],
            'cast.*.character_name' => ['nullable', 'string', 'max:150'],
            'cast.*.sort_order'       => ['nullable', 'integer', 'min:0'],

            // ملفات الفيديو
            'video_files'                  => ['sometimes', 'array'],
            'video_files.*.video_type'     => ['required', 'in:main,trailer,teaser,clip'],
            'video_files.*.quality'        => ['required', 'in:240p,360p,480p,720p,1080p,4k'],
            'video_files.*.file'           => $this->isUpdate()
                ? ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/x-matroska']
                : ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/x-matroska'],
            'video_files.*.file_url'       => $this->isUpdate()
                ? ['nullable', 'url', 'max:2000']
                : ['nullable', 'url', 'max:2000', 'required_without:video_files.*.file'],
            'video_files.*.format'         => ['nullable', 'string', 'max:20'],
            'video_files.*.source_type'    => ['nullable', 'string', 'max:20', 'in:url,file'],

            // الترجمات
            'subtitles'              => ['nullable', 'array'],
            'subtitles.*.language'   => ['required', 'string', 'max:10', 'distinct'],
            'subtitles.*.label'      => ['required', 'string', 'max:100', 'distinct'],
            'subtitles.*.file'       => $this->isUpdate()
                ? ['nullable', 'file', 'mimes:srt,vtt']
                : ['nullable', 'file', 'mimes:srt,vtt'],
            'subtitles.*.url'        => $this->isUpdate()
                ? ['nullable', 'url', 'max:2000']
                : ['nullable', 'url', 'max:2000', 'required_without:subtitles.*.file'],
            'subtitles.*.is_default' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'video_files.*.quality.distinct'      => __('admin.validation_quality_distinct'),
            'video_files.*.file_url.required_without' => __('admin.validation_url_or_file'),
            'subtitles.*.language.distinct' => __('admin.validation_language_distinct'),
            'subtitles.*.label.distinct'    => __('admin.validation_label_distinct'),
            'subtitles.*.url.required_without' => __('admin.validation_subtitle_url_or_file'),
            'subtitles.*.file.mimes'        => __('admin.validation_subtitle_mimes'),
        ];
    }

    /** تأكيد "واحد فقط افتراضي" على السيرفر */
    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $subs = collect($this->input('subtitles', []));
            $defaults = $subs->filter(fn($s) => !empty($s['is_default']))->count();
            if ($defaults > 1) {
                $v->errors()->add('subtitles', __('admin.validation_one_default_subtitle'));
            }
        });
    }
}
