<div class="row">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
    @endpush

    @php
        $locale = app()->getLocale();
        // القيم المختارة مسبقًا (للـ edit) + دعم old() عند فشل التحقق
        $selectedCategories = old('category_ids', isset($movie) ? $movie->categories->pluck('id')->toArray() : []);
        $selectedPeople     = old('person_ids',   isset($movie) ? $movie->people->pluck('id')->toArray()     : []);
    @endphp

    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- العناوين --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_Title_Film_in_Arabic') }}" :value="old('title_ar', $movie->title_ar)" name="title_ar"
                                      placeholder="{{ __('admin.Name_Title_Film_in_Arabic_placeholder') }}" required autofocus />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_Title_Film_in_English') }}" :value="old('title_en', $movie->title_en)" name="title_en"
                                      placeholder="{{ __('admin.Name_Title_Film_in_English_placeholder') }}" />
                    </div>

                    {{-- الأوصاف --}}
                    <div class="mb-4 col-md-6">
                        <x-form.textarea label="{{ __('admin.desecription_ar') }}" name="description_ar" rows="2"
                                         :value="old('description_ar', $movie->description_ar)"
                                         placeholder="{{ __('admin.desecription_ar_placeholder') }}" />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.textarea label="{{ __('admin.desecription_en') }}" name="description_en" rows="2"
                                         :value="old('description_en', $movie->description_en)"
                                         placeholder="{{ __('admin.desecription_en_placeholder') }}" />
                    </div>

                    {{-- الحالة --}}
                    <div class="mb-4 col-md-6">
                        <x-form.selectkey label="{{ __('admin.Status') }}" name="status" required
                                          :selected="old('status', $movie->status ?? 'draft')"
                                          :options="$statusOptions" />
                    </div>

                    {{-- مميز --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.Star') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                   value="1" @checked(old('is_featured', $movie->is_featured))>
                            <label class="form-check-label" for="is_featured">عرض كفيلم مميز</label>
                        </div>
                    </div>

                    {{-- التريلر --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="رابط التريلر" :value="old('trailer_url', $movie->trailer_url)" name="trailer_url"
                                      placeholder="https://youtube.com/..." />
                    </div>
                </div>
            </div>
        </div>

        {{-- 🆕 التصنيفات + الأشخاص (Many-to-Many) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- التصنيفات --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{__('admin.categories')}}</label>
                        <select class="form-control" name="category_ids[]" multiple>
                            @foreach($categories as $cat)
                                @php
                                    $label = $locale === 'ar'
                                        ? ($cat->name_ar ?? $cat->name_en)
                                        : ($cat->name_en ?: $cat->name_ar);
                                @endphp
                                <option value="{{ $cat->id }}" @selected(in_array($cat->id, (array)$selectedCategories))>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{__('admin.select_categories')}}</small>
                    </div>

                    {{-- الأشخاص (ممثلون/فريق العمل) --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{__('admin.people')}}</label>
                        <select class="form-control" name="person_ids[]" multiple>
                            @foreach($people as $p)
                                @php
                                    $pLabel = $locale === 'ar'
                                        ? ($p->name_ar ?? $p->name_en)
                                        : ($p->name_en ?: $p->name_ar);
                                @endphp
                                <option value="{{ $p->id }}" @selected(in_array($p->id, (array)$selectedPeople))>
                                    {{ $pLabel }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{__('admin.select_people')}}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- تاريخ/مدة/تقييم --}}
                    <div class="mb-4 col-md-4">
                        <x-form.input type="date" label="{{__('admin.release_date')}}"
                                      :value="old('release_date', $movie->release_date?->format('Y-m-d'))"
                                      name="release_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="{{__('admin.duration_minutes')}}"
                                      :value="old('duration_minutes', $movie->duration_minutes)"
                                      name="duration_minutes" placeholder="120" min="0" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" step="0.1" label="تقييم IMDb (0-10)"
                                      :value="old('imdb_rating', $movie->imdb_rating)"
                                      name="imdb_rating" placeholder="7.8" min="0" max="10" />
                    </div>

                    {{-- التصنيف/اللغة/الدولة --}}
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{__('admin.content_rating')}}" name="content_rating"
                                          :selected="old('content_rating', $movie->content_rating ?? 'G')"
                                          :options="$contentRatingOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{__('admin.language')}}" name="language"
                                          :selected="old('language', $movie->language ?? 'ar')"
                                          :options="$languageOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{__('admin.country')}}" name="country"
                                          :selected="old('country', $movie->country)"
                                          :options="$countries" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الروابط/الرفع: بوستر وخلفية --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="url" label="{{__('admin.poster_url_out')}}"
                                      :value="old('poster_url_out', $movie->poster_url)"
                                      name="poster_url_out"
                                      placeholder="أو اختر من الوسائط" />
                        <input type="text" id="imageInput" name="poster_url"
                               value="{{ old('poster_url', $movie->poster_url) }}"
                               class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#mediaModal"
                                    data-clear-btn="#clearImageBtn1"
                                    data-img="#poster_img"
                                    data-mode="single"
                                    data-input="#imageInput"
                                    class="mt-3 btn btn-primary openMediaModal">
                                اختر من الوسائط
                            </button>
                            <button type="button"
                                    class="clear-btn mt-3 btn btn-danger {{ !empty($movie->poster_url) ? '' : 'd-none' }}"
                                    id="clearImageBtn1"
                                    data-img="#poster_img"
                                    data-input="#imageInput">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $movie->poster_full_url }}"
                                 alt="poster" id="poster_img"
                                 class="{{ !empty($movie->poster_url) ? '' : 'd-none' }}"
                                 style="max-height:100px">
                        </div>
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="url" label="{{__('admin.backdrop_url_out')}}"
                                      :value="old('backdrop_url_out', $movie->backdrop_url)"
                                      name="backdrop_url_out"
                                      placeholder="أو اختر من الوسائط" />
                        <input type="text" id="imageInput2" name="backdrop_url"
                               value="{{ old('backdrop_url', $movie->backdrop_url) }}"
                               class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#mediaModal"
                                    data-clear-btn="#clearImageBtn2"
                                    data-img="#backdrop_img"
                                    data-mode="single"
                                    data-input="#imageInput2"
                                    class="mt-3 btn btn-primary openMediaModal">
                                اختر من الوسائط
                            </button>
                            <button type="button"
                                    class="clear-btn mt-3 btn btn-danger {{ !empty($movie->backdrop_url) ? '' : 'd-none' }}"
                                    id="clearImageBtn2"
                                    data-img="#backdrop_img"
                                    data-input="#imageInput2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $movie->backdrop_full_url }}"
                                 alt="backdrop" id="backdrop_img"
                                 class="{{ !empty($movie->backdrop_url) ? '' : 'd-none' }}"
                                 style="max-height:100px">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TMDB وعداد المشاهدات (اختياري) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" min="0" label="TMDB ID"
                                      :value="old('tmdb_id', $movie->tmdb_id)" name="tmdb_id"
                                      placeholder="مثال: 550" />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" min="0" label="{{__('admin.view_count')}}"
                                      :value="old('view_count', $movie->view_count ?? 0)"
                                      name="view_count" placeholder="0" readonly />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                {{ $btn_label ?? 'أضف' }}
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeDeleteModal">{{__('admin.Cancel')}}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">نعم، حذف</button>
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
