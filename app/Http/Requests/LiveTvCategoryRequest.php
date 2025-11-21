<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LiveTvCategoryRequest extends FormRequest
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
        $liveTvCategory = $this->route('live_tv_category');
        $id = $liveTvCategory ? ($liveTvCategory->id ?? $liveTvCategory) : null;

        $rules = [
            'name_ar'           => ['required', 'string', 'max:100'],
            'name_en'           => ['required', 'string', 'max:100'],
            'slug'              => ['nullable', 'string', 'max:150', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description_ar'    => ['nullable', 'string', 'max:1000'],
            'description_en'    => ['nullable', 'string', 'max:1000'],
            'icon_url'          => ['nullable', 'string', 'max:1000'],
            'icon_url_out'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'cover_image_url'   => ['nullable', 'string', 'max:1000'],
            'cover_image_url_out' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'sort_order'        => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured'       => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ];

        // Unique validation
        if ($this->isUpdate() && $id) {
            $rules['name_ar'][] = 'unique:live_tv_categories,name_ar,' . $id;
            $rules['name_en'][] = 'unique:live_tv_categories,name_en,' . $id;
            $rules['slug'][] = 'unique:live_tv_categories,slug,' . $id;
        } else {
            $rules['name_ar'][] = 'unique:live_tv_categories,name_ar';
            $rules['name_en'][] = 'unique:live_tv_categories,name_en';
            $rules['slug'][] = 'unique:live_tv_categories,slug';
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
            'sort_order.min'   => __('admin.sort_order_min'),
            'sort_order.max'   => __('admin.sort_order_max'),
            'icon_url_out.image' => 'الأيقونة يجب أن تكون صورة',
            'icon_url_out.mimes' => 'الأيقونة يجب أن تكون من نوع: jpeg, png, jpg, gif, svg',
            'icon_url_out.max'   => 'حجم الأيقونة يجب ألا يتجاوز 2 ميجابايت',
            'cover_image_url_out.image' => 'صورة الغلاف يجب أن تكون صورة',
            'cover_image_url_out.mimes' => 'صورة الغلاف يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'cover_image_url_out.max'   => 'حجم صورة الغلاف يجب ألا يتجاوز 5 ميجابايت',
        ];
    }
}
