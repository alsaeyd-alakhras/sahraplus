<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShortRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }


    //  protected function isUpdate(): bool
    // {
    //     return $this->isMethod('PUT') || $this->isMethod('PATCH');
    // }
    
   public function rules(): array
{
    $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

    return [
        'title'        => ['required','string','max:200'],
        'description'  => ['nullable','string'],
        'share_url'    => ['nullable','url','max:191'],
        'aspect_ratio' => ['required', Rule::in(['vertical','horizontal'])],
        'status'       => ['required', Rule::in(['active','inactive'])],
        'is_featured'  => ['sometimes','boolean'],

        // ممنوعة من الطلب
        'likes_count'    => ['prohibited'],
        'comments_count' => ['prohibited'],
        'shares_count'   => ['prohibited'],

        // التصنيفات
        'category_ids'   => ['nullable','array'],
        'category_ids.*' => ['integer','exists:movie_categories,id'],

        // البوستر: رابط خارجي أو ملف أو محلي
        'poster_path_out' => ['nullable','url','max:191'],
        'posterUpload'    => ['nullable','image','max:2048'],
        'poster_path'     => ['nullable','string','max:191'],

        // الفيديو الرئيسي: رابط خارجي أو ملف أو محلي
        'video_path_out' => ['nullable','url','max:191'],
        'videoUpload'    => ['nullable','file','mimetypes:video/mp4,video/webm,video/quicktime,video/x-matroska'],
        'video_path'     => ['nullable','string','max:191'],

        // video_files (مثل الأفلام)
        'video_files'                  => ['sometimes','array'],
        'video_files.*.video_type'     => ['required','in:main,trailer,teaser,clip'],
        'video_files.*.quality'        => ['required','in:240p,360p,480p,720p,1080p,4k'],
        'video_files.*.file'           => ['nullable','file','mimetypes:video/mp4,video/webm,video/quicktime,video/x-matroska'],
        'video_files.*.file_url'       => ['nullable','url','max:2000','required_without:video_files.*.file'],
        'video_files.*.format'         => ['nullable','string','max:20'],
    ];
}


    public function messages(): array
    {
        return [
            'video_files.*.file_url.required_without' => __('admin.validation_url_or_file'),
        ];
    }
}
