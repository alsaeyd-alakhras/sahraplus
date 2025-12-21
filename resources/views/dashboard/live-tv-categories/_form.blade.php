<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_ar') }}" :value="$category->name_ar" name="name_ar"
                            placeholder="{{ __('admin.category_name_ar_placeholder') }}" required autofocus />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_en') }}" :value="$category->name_en" name="name_en"
                            placeholder="{{ __('admin.category_name_en_placeholder') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Slug_optional') }}" :value="$category->slug" name="slug"
                            placeholder="{{ __('admin.category_slug_placeholder') }}" />
                        <small class="text-muted">{{ __('admin.slug_hint') }}</small>
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.Sort_order') }}"
                            :value="$category->sort_order ?? 0" name="sort_order" placeholder="0" min="0" max="9999" />
                        <small class="text-muted">{{ __('admin.sort_order_hint') }}</small>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Description_ar') }}</label>
                        <textarea class="form-control" name="description_ar" rows="4"
                            placeholder="{{ __('admin.description_ar_placeholder') }}">{{ old('description_ar', $category->description_ar) }}</textarea>
                        @error('description_ar')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Description_en') }}</label>
                        <textarea class="form-control" name="description_en" rows="4"
                            placeholder="{{ __('admin.description_en_placeholder') }}">{{ old('description_en', $category->description_en) }}</textarea>
                        @error('description_en')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Icon') }}</label>

                        @if(isset($category->icon_url) && $category->icon_url)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $category->icon_url) }}" alt="Icon" class="img-thumbnail"
                                style="max-height: 100px;">
                        </div>
                        @endif

                        <input type="file" name="icon_url_out" class="form-control" accept="image/*"
                            onchange="previewImage(this, 'icon-preview')">

                        <img id="icon-preview" class="img-thumbnail mt-2" style="max-height: 150px; display: none;">

                        <small class="text-muted d-block mt-1">
                            {{ __('admin.icon_hint') }}
                        </small>

                        @error('icon_url')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Cover_image') }}</label>

                        @if(isset($category->cover_image_url) && $category->cover_image_url)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $category->cover_image_url) }}" alt="Cover"
                                class="img-thumbnail" style="max-height: 100px;">
                        </div>
                        @endif

                        <input type="file" name="cover_image_url_out" class="form-control" accept="image/*"
                            onchange="previewImage(this, 'cover-preview')">

                        <img id="cover-preview" class="img-thumbnail mt-2" style="max-height: 150px; display: none;">

                        <small class="text-muted d-block mt-1">
                            {{ __('admin.cover_hint') }}
                        </small>

                        @error('cover_image_url')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.Is_featured') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" @checked($category->is_featured ?? false)>
                            <label class="form-check-label" for="is_featured">{{ __('admin.featured_label') }}</label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.Status') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                @checked($category->is_active ?? true)>
                            <label class="form-check-label" for="is_active">{{ __('admin.active_label') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard.live-tv-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> {{ __('admin.Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> {{ $btn_label ?? __('admin.Save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush