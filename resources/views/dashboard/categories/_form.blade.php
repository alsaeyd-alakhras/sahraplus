<div class="row">
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
    @endpush
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @php
        $locale = app()->getLocale();
    @endphp

    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- العناوين --}}
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.Name_ar') }}" :value="old('name_ar', $movie_category->name_ar)" name="name_ar" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.Name_en') }}" :value="old('name_en', $movie_category->name_en)" name="name_en" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الأوصاف --}}
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.desecription_ar') }}" :value="old('description_ar', $movie_category->description_ar)" name="description_ar" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.desecription_en') }}" :value="old('description_en', $movie_category->description_en)" name="description_en" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-6">

                        @php
                            $image_url = Str::startsWith($movie_category->image_url, ['http', 'https']);
                            $image_url_out = ($image_url ? $movie_category->image_url : null);
                        @endphp
                        <x-form.input type="url" label="{{ __('admin.Photo') }}" :value="$image_url_out" name="image_url_out"
                            placeholder="أو اختر من الوسائط" />

                        <input type="text" id="imageInput" name="image_url"
                            value="{{ old('image_url', $movie_category->image_url) }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn1" data-img="#poster_img" data-mode="single"
                                data-input="#imageInput" class="mt-3 btn btn-primary openMediaModal">
                                {{ __('admin.choose_from_media') }}
                            </button>
                            <button type="button"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($movie_category->image_url) ? '' : 'd-none' }}"
                                id="clearImageBtn1" data-img="#poster_img" data-input="#imageInput">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $movie_category->poster_full_url }}" alt="poster" id="poster_img"
                                class="{{ !empty($movie_category->image_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">{{__('admin.Status')}}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $movie_category->is_active))>
                            <label class="form-check-label">{{__('admin.Active')}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-3">
                        <x-form.input label="{{ __('admin.color') }}" :value="old('color', $movie_category->color)" name="color" placeholder="#FF9900" />
                    </div>
                    <div class="col-md-3">
                        <x-form.input type="number" label="{{ __('admin.Sort_order') }}" :value="old('sort_order', $movie_category->sort_order ?? 0)" name="sort_order" min="0" />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                {{ $btn_label ?? __('admin.Save') }}
            </button>
        </div>
    </div>
</div>

{{-- مودال الوسائط --}}
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-6 text-2xl font-bold modal-title">{{ __('admin.media') }} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeMediaModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 modal-body">
                <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="file" name="image" id="imageInputMedia" class="mb-2 form-control">
                    <button type="button" id="uploadFormBtn" class="btn btn-primary">{{ __('admin.upload') }}</button>
                </form>
                <div id="mediaGrid" class="masonry">
                    {{-- الصور ستُملأ تلقائيًا عبر jQuery --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="selectMediaBtn">{{ __('admin.select') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
    aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('admin.Delete Confirmation')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
               {{ __('admin.Are you sure?') }}
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="closeDeleteModal">إلغاء</button>

                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('admin.Save') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // media
        const urlIndex = "{{ route('dashboard.media.index') }}";
        const urlStore = "{{ route('dashboard.media.store') }}";
        const urlDelete = "{{ route('dashboard.media.destroy', ':id') }}";
        const _token = "{{ csrf_token() }}";
        const urlAssetPath = "{{ config('app.asset_url') }}";
    </script>
    <script src="{{ asset('js/custom/mediaPage.js') }}"></script>
@endpush
