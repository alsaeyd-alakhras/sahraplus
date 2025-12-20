<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LiveTvChannelRequest extends FormRequest
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
        $liveTvChannel = $this->route('live_tv_channel');
        $id = $liveTvChannel ? ($liveTvChannel->id ?? $liveTvChannel) : null;

        $rules = [
            'name_ar'           => ['required', 'string', 'max:100'],
            'name_en'           => ['required', 'string', 'max:100'],
            'slug'              => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'category_id'       => ['required', 'exists:live_tv_categories,id'],
            'description_ar'    => ['nullable', 'string'],
            'description_en'    => ['nullable', 'string'],
            'logo_url'          => ['nullable', 'string', 'max:1000'],
            'logo_url_out'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'poster_url'        => ['nullable', 'string', 'max:1000'],
            'poster_url_out'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'stream_url'        => ['required', 'string', 'max:100'],
            'stream_type'       => ['required', 'in:hls,dash,rtmp'],
            'epg_id'            => ['nullable', 'string', 'max:255'],
            'language'          => ['nullable', 'string', 'max:10'],
            'country'           => ['nullable', 'string', 'max:4'],
            'sort_order'        => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured'       => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ];

        // Unique validation
        if ($this->isUpdate() && $id) {
            $rules['name_ar'][] = 'unique:live_tv_channels,name_ar,' . $id;
            $rules['name_en'][] = 'unique:live_tv_channels,name_en,' . $id;
            $rules['slug'][] = 'unique:live_tv_channels,slug,' . $id;
        } else {
            $rules['name_ar'][] = 'unique:live_tv_channels,name_ar';
            $rules['name_en'][] = 'unique:live_tv_channels,name_en';
            $rules['slug'][] = 'unique:live_tv_channels,slug';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => __('admin.name_ar_required'),
            'name_ar.unique'   => __('admin.name_ar_unique'),
            'name_en.required' => __('admin.name_en_required'),
            'name_en.unique'   => __('admin.name_en_unique'),
            'slug.unique'      => __('admin.slug_unique'),
            'slug.regex'       => __('admin.slug_format'),
            'category_id.required' => 'الفئة مطلوبة',
            'category_id.exists'   => 'الفئة المختارة غير موجودة',
            'stream_url.required'  => 'اسم البث مطلوب',
            'stream_url.max'       => 'اسم البث يجب ألا يتجاوز 100 حرف',
            'stream_type.required' => 'نوع البث مطلوب',
            'stream_type.in'       => 'نوع البث يجب أن يكون HLS أو DASH أو RTMP',
            'sort_order.min'   => __('admin.sort_order_min'),
            'sort_order.max'   => __('admin.sort_order_max'),
            'logo_url_out.image' => 'الشعار يجب أن يكون صورة',
            'logo_url_out.mimes' => 'الشعار يجب أن يكون من نوع: jpeg, png, jpg, gif, svg',
            'logo_url_out.max'   => 'حجم الشعار يجب ألا يتجاوز 2 ميجابايت',
            'poster_url_out.image' => 'صورة الغلاف يجب أن تكون صورة',
            'poster_url_out.mimes' => 'صورة الغلاف يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'poster_url_out.max'   => 'حجم صورة الغلاف يجب ألا يتجاوز 5 ميجابايت',
        ];
    }
}
