<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChannelProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function isUpdate(): bool
    {
        return $this->route('channel_program') ? true : false;
    }

    public function rules(): array
    {
        $id = $this->route('channel_program');

        return [
            'channel_id' => ['required', 'integer', 'exists:live_tv_channels,id'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'genre' => ['nullable', 'string', 'max:100'],
            'is_live' => ['nullable', 'boolean'],
            'is_repeat' => ['nullable', 'boolean'],
            'poster_url' => ['nullable', 'string'],
            'poster_url_out' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'channel_id.required' => __('admin.channel_id_required'),
            'channel_id.exists' => __('admin.channel_id_exists'),
            'title_ar.required' => __('admin.title_ar_required'),
            'title_ar.max' => __('admin.title_ar_max'),
            'title_en.max' => __('admin.title_en_max'),
            'start_time.required' => __('admin.start_time_required'),
            'start_time.date' => __('admin.start_time_date'),
            'end_time.required' => __('admin.end_time_required'),
            'end_time.date' => __('admin.end_time_date'),
            'end_time.after' => __('admin.end_time_after_start'),
        ];
    }
}
