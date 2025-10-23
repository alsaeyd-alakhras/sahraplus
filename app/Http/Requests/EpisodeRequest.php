<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EpisodeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH');
    }

    public function rules(): array
    {
        return [
            'season_id'       => ['required','integer','exists:seasons,id'],
            'episode_number'  => ['required','integer','min:1'],
            'title_ar'        => ['required','string','max:200'],
            'title_en'        => ['required','string','max:200'],
            'description_ar'  => ['nullable','string'],
            'description_en'  => ['nullable','string'],
            'thumbnail_url'       => ['nullable','string','max:1000'],
            'thumbnail_url_out'   => ['nullable','string','max:1000'],
            'air_date'        => ['nullable','date'],
            'duration_minutes'=> ['nullable','integer','min:0'],
            'imdb_rating'     => ['nullable','numeric','min:0','max:10'],
            'status'          => ['required', Rule::in(['draft','published','archived'])],
            'view_count'      => ['nullable','integer','min:0'],
            'tmdb_id'         => ['nullable','string','max:20'],
            'intro_skip_time'  => ['required'],

            // الفيديوهات
            'video_files'                  => ['sometimes','array'],
            'video_files.*.video_type'     => ['required','in:main,trailer,teaser,clip'],
            'video_files.*.quality'        => ['required','in:240p,360p,480p,720p,1080p,4k'],
            'video_files.*.file'           => $this->isUpdate()
                ? ['nullable','file','mimetypes:video/mp4,video/webm,video/quicktime,video/x-matroska','max:204800']
                : ['nullable','file','mimetypes:video/mp4,video/webm,video/quicktime,video/x-matroska','max:204800'],
            'video_files.*.file_url'       => $this->isUpdate()
                ? ['nullable','url','max:2000']
                : ['nullable','url','max:2000','required_without:video_files.*.file'],
            'video_files.*.format'         => ['nullable','string','max:20'],
            'video_files.*.source_type'    => ['nullable','string','in:url,file'],

            // الترجمات
            'subtitles'               => ['nullable','array'],
            'subtitles.*.language'    => ['required','string','max:10','distinct'],
            'subtitles.*.label'       => ['required','string','max:100','distinct'],
            'subtitles.*.file'        => $this->isUpdate()
                ? ['nullable','file','mimes:srt,vtt,ass']
                : ['nullable','file','mimes:srt,vtt,ass'],
            // ملاحظة: الفورم ممكن يرسل url أو file_url — نسمح بالاثنين
            'subtitles.*.url'         => $this->isUpdate()
                ? ['nullable','url','max:2000']
                : ['nullable','url','max:2000','required_without:subtitles.*.file'],
            'subtitles.*.file_url'    => ['nullable','url','max:2000'],
            'subtitles.*.is_default'  => ['nullable','boolean'],
            'subtitles.*.is_forced'   => ['nullable','boolean'],
            'subtitles.*.source_type' => ['nullable','in:url,file'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $subs = collect($this->input('subtitles', []));
            $defaults = $subs->filter(fn($s)=>!empty($s['is_default']))->count();
            if ($defaults > 1) {
                $v->errors()->add('subtitles', __('admin.validation_one_default_subtitle'));
            }
        });
    }
}
