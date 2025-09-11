<div class="row">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
    @endpush
    <div class="col-md-12">
        {{-- القسم الأول: العناوين --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.title_ar') }}" name="title_ar" :value="$series->title_ar"
                            placeholder="{{ __('admin.title_ar_placeholder') }}" required autofocus />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.title_en') }}" name="title_en" :value="$series->title_en"
                            placeholder="{{ __('admin.title_en_placeholder') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثاني: الأوصاف --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_ar') }}" name="description_ar" rows="2" :value="$series->description_ar"
                            placeholder="{{ __('admin.description_ar_placeholder') }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_en') }}" name="description_en" rows="2" :value="$series->description_en"
                            placeholder="{{ __('admin.description_en_placeholder') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثالث: الحالة --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.status') }}" name="status" :selected="$series->status ?? 'draft'" :options="$statusOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.series_status') }}" name="series_status" :selected="$series->series_status ?? 'returning'"
                            :options="$seriesStatusOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.is_featured') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" @checked($series->is_featured)>
                            <label class="form-check-label" for="is_featured">{{ __('admin.is_featured_label') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الرابع: التواريخ والإحصاءات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-4">
                        <x-form.input type="date" label="{{ __('admin.first_air_date') }}" :value="$series->first_air_date?->format('Y-m-d')" name="first_air_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="date" label="{{ __('admin.last_air_date') }}" :value="$series->last_air_date?->format('Y-m-d')" name="last_air_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" step="0.1" min="0" max="10" label="{{ __('admin.imdb_rating') }}"
                            :value="$series->imdb_rating" name="imdb_rating" placeholder="7.5" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" min="0" label="{{ __('admin.seasons_count') }}" :value="$series->seasons_count"
                            name="seasons_count" placeholder="5" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" min="0" label="{{ __('admin.episodes_count') }}" :value="$series->episodes_count"
                            name="episodes_count" placeholder="100" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" min="0" label="{{ __('admin.view_count') }}" :value="$series->view_count ?? 0"
                            name="view_count" readonly />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الخامس: التصنيفات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.content_rating') }}" name="content_rating" :selected="$series->content_rating"
                            :options="$contentRatingOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.language') }}" name="language" :selected="$series->language ?? 'ar'" :options="$languageOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.country') }}" name="country" :selected="$series->country" :options="$countries" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم السادس: الصور والروابط --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- بوستر --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="url" label="{{ __('admin.poster_url') }}" :value="$series->poster_url" name="poster_url_out"
                            placeholder="{{ __('admin.poster_url_placeholder') }}" />
                        <input type="text" id="posterInput" name="poster_url" value="{{ $series->poster_url }}"
                            class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearPosterBtn" data-img="#poster_img" data-mode="single"
                                data-input="#posterInput" class="mt-3 btn btn-primary openMediaModal">
                                {{ __('admin.choose_from_media') }}
                            </button>
                            <button type="button" id="clearPosterBtn"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($series->poster_url) ? '' : 'd-none' }}"
                                data-img="#poster_img" data-input="#posterInput">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $series->poster_full_url }}" alt="poster" id="poster_img"
                                class="{{ !empty($series->poster_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                    </div>

                    {{-- خلفية --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="url" label="{{ __('admin.backdrop_url') }}" :value="$series->backdrop_url" name="backdrop_url_out"
                            placeholder="{{ __('admin.backdrop_url_placeholder') }}" />
                        <input type="text" id="backdropInput" name="backdrop_url"
                            value="{{ $series->backdrop_url }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearBackdropBtn" data-img="#backdrop_img" data-mode="single"
                                data-input="#backdropInput" class="mt-3 btn btn-primary openMediaModal">
                                {{ __('admin.choose_from_media') }}
                            </button>
                            <button type="button" id="clearBackdropBtn"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($series->backdrop_url) ? '' : 'd-none' }}"
                                data-img="#backdrop_img" data-input="#backdropInput">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $series->backdrop_full_url }}" alt="backdrop" id="backdrop_img"
                                class="{{ !empty($series->backdrop_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- تريلر --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="url" label="{{ __('admin.trailer_url') }}" :value="$series->trailer_url" name="trailer_url"
                            placeholder="https://youtube.com/..." />
                    </div>
                    {{-- TMDB --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" min="0" label="{{ __('admin.tmdb_id') }}" :value="$series->tmdb_id"
                            name="tmdb_id" placeholder="مثال: 1412" />
                    </div>
                </div>
            </div>
        </div>

        {{-- زر الحفظ --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                {{ $btn_label ?? __('admin.save') }}
            </button>
        </div>

    </div>
</div>
{{-- مودال الوسائط --}}
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-6 text-2xl font-bold modal-title">📁 {{ __('admin.media_library') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeMediaModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 modal-body">
                <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="file" name="image" id="imageInputMedia" class="mb-2 form-control">
                    <button type="button" id="uploadFormBtn" class="btn btn-primary">{{ __('admin.upload_image') }}</button>
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
                <h5 class="modal-title">{{ __('admin.confirm_delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                {{ __('admin.confirm_delete_message') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="closeDeleteModal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('admin.delete') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const urlIndex = "{{ route('dashboard.media.index') }}";
        const urlStore = "{{ route('dashboard.media.store') }}";
        const urlDelete = "{{ route('dashboard.media.destroy', ':id') }}";
        const _token = "{{ csrf_token() }}";
        const urlAssetPath = "{{ config('app.asset_url') }}";
    </script>
    <script src="{{ asset('js/custom/mediaPage.js') }}"></script>
@endpush
