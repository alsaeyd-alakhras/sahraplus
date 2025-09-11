<div class="row">
    <div class="mb-4 col-md-6">
        <x-form.input label="{{ __('admin.Name_ar') }}" :value="old('name_ar', $movie_category->name_ar)" name="name_ar" required />
    </div>
    <div class="mb-4 col-md-6">
        <x-form.input label="{{ __('admin.Name_en') }}" :value="old('name_en', $movie_category->name_en)" name="name_en" required />
    </div>

    <div class="mb-4 col-md-6">
        <x-form.input label="{{ __('admin.Slug') }}" :value="old('slug', $movie_category->slug)" name="slug" placeholder="movie-category" />
    </div>
    <div class="mb-4 col-md-6">
        <x-form.input label="{{ __('admin.Photo') }}":value="old('image_url', $movie_category->image_url)" name="image_url" />
    </div>

    <div class="mb-4 col-md-6">
        <x-form.input label="{{ __('admin.desecription_ar') }}" :value="old('description_ar', $movie_category->description_ar)" name="description_ar" />
    </div>
    <div class="mb-4 col-md-6">
        <x-form.input label="{{ __('admin.desecription_en') }}" :value="old('description_en', $movie_category->description_en)" name="description_en" />
    </div>

    <div class="mb-4 col-md-3">
        <x-form.input label="{{ __('admin.color') }}" :value="old('color', $movie_category->color)" name="color" placeholder="#FF9900" />
    </div>
    <div class="mb-4 col-md-3">
        <x-form.input type="number" label="{{ __('admin.Sort_order') }}" :value="old('sort_order', $movie_category->sort_order ?? 0)" name="sort_order" min="0" />
    </div>
    <div class="mb-4 col-md-6">
        <label class="form-label d-block">{{__('admin.Status')}}</label>
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $movie_category->is_active))>
            <label class="form-check-label">{{__('admin.Active')}}</label>
        </div>
    </div>

    <div class="mb-3 border shadow card border-1">
            <div class="card-body">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        {{ $btn_label ?? 'أضف' }}
                    </button>
                </div>
            </div>
        </div>

        
</div>
