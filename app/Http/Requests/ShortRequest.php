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

    public function rules(): array
    {
        return [
            'title'       => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'video_path'  => ['nullable','string','max:191'],
            'poster_path' => ['nullable','string','max:191'],
            'aspect_ratio'=> ['required', Rule::in(['vertical','horizontal'])],
            'share_url'   => ['nullable','string','max:191'],
            'is_featured' => ['sometimes','boolean'],
            'status'      => ['required', Rule::in(['active','inactive'])],

            // Uploads
            'posterUpload'=> ['nullable','file','mimes:jpg,jpeg,png,webp','max:8192'],
            'videoUpload' => ['nullable','file','mimes:mp4,mov,avi,webm','max:51200'],
        ];
    }
}
