<div class="row">
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

                    <div class="col-md-6">
                        <x-form.input label="Logo Url" :value="$series->logo_url" name="logo_url"
                            placeholder="https://example.com" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثاني: الأوصاف --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_ar') }}" name="description_ar" rows="2"
                            :value="$series->description_ar" placeholder="{{ __('admin.description_ar_placeholder') }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_en') }}" name="description_en" rows="2"
                            :value="$series->description_en" placeholder="{{ __('admin.description_en_placeholder') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثالث: الحالة --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-4">
                        <x-form.selectkey label="{{ __('admin.status') }}" name="status" :selected="$series->status ?? 'draft'"
                            :options="$statusOptions" />
                    </div>

                    <div class="col-md-4">
                        <x-form.selectkey label="{{ __('admin.series_status') }}" name="series_status"
                            :selected="$series->series_status ?? 'returning'" :options="$seriesStatusOptions" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">{{ __('admin.is_featured') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" @checked($series->is_featured)>
                            <label class="form-check-label" for="is_featured">{{ __('admin.is_featured') }}</label>
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
                        <x-form.input type="date" label="{{ __('admin.first_air_date') }}" :value="$series->first_air_date?->format('Y-m-d')"
                            name="first_air_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="date" label="{{ __('admin.last_air_date') }}" :value="$series->last_air_date?->format('Y-m-d')"
                            name="last_air_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" step="0.1" min="0" max="10"
                            label="{{ __('admin.imdb_rating') }}" :value="$series->imdb_rating" name="imdb_rating"
                            placeholder="7" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input type="number" min="0" label="{{ __('admin.seasons_count') }}"
                            :value="$series->seasons_count" name="seasons_count" placeholder="5" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input type="number" min="0" label="{{ __('admin.episodes_count') }}"
                            :value="$series->episodes_count" name="episodes_count" placeholder="100" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input type="number" min="0" label="{{ __('admin.view_count') }}"
                            :value="$series->view_count ?? 0" name="view_count" />
                    </div>
                </div>
            </div>
        </div>

        {{-- … أعلى الفورم كالمعتاد … --}}

        {{-- 🆕 التصنيفات (category_series_pivot) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- Categories --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">
                            {{ __('admin.series Category') }}
                        </label>

                        <select name="category_ids[]" id="category-select" class="form-control select2" multiple
                            data-placeholder="{{ __('admin.select_categories') }}">
                            @foreach ($allCategories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, old('category_ids', $series->categories->pluck('id')->toArray() ?? []))
                                        ? 'selected'
                                        : '' }}>
                                    {{ $category->name_ar }}
                                </option>
                            @endforeach
                        </select>

                        <span class="text-muted d-block mt-2">
                            {{ __('admin.select_at_least_one_category') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🆕 الطاقم (series_cast) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.Cast') }}</label>
                            <button type="button" id="add-cast-row" class="btn btn-dark btn-sm">+
                                {{ __('admin.Create') }}</button>
                        </div>

                        <div id="cast-rows" class="gap-3 d-grid">
                            @php
                                $oldCast = old(
                                    'cast',
                                    isset($series)
                                        ? $series->people
                                            ->map(function ($p) {
                                                return [
                                                    'person_id' => $p->id,
                                                    'person_name' => $p->name_ar ?? $p->name_en,
                                                    'role_type' => $p->pivot->role_type,
                                                    'character_name' => $p->pivot->character_name,
                                                    'sort_order' => $p->pivot->sort_order,
                                                    'id' => $p->pivot->id,
                                                ];
                                            })
                                            ->toArray()
                                        : [],
                                );
                                $roleTypes = [
                                    'actor' => __('admin.actor'),
                                    'director' => __('admin.director'),
                                    'writer' => __('admin.writer'),
                                    'producer' => __('admin.producer'),
                                    'cinematographer' => __('admin.cinematographer'),
                                    'composer' => __('admin.composer'),
                                ];
                            @endphp

                            @forelse($oldCast as $i => $row)
                                @include('dashboard.series.partials._cast_row', [
                                    'i' => $i,
                                    'row' => $row,
                                    'allPeople' => $allPeople ?? collect(),
                                    'roleTypes' => $roleTypes,
                                ])
                            @empty
                                @include('dashboard.series.partials._cast_row', [
                                    'i' => 0,
                                    'row' => [],
                                    'allPeople' => $allPeople ?? collect(),
                                    'roleTypes' => $roleTypes,
                                ])
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- القسم الخامس: التصنيفات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.content_rating') }}" name="content_rating"
                            :selected="$series->content_rating" :options="$contentRatingOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.language') }}" name="language" :selected="$series->language ?? 'ar'"
                            :options="$languageOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.country') }}" name="country" :selected="$series->country"
                            :options="$countries" />
                    </div>
                    {{-- تريلر --}}
                    <div class="col-md-6">
                        <x-form.input type="url" label="{{ __('admin.trailer_url') }}" :value="$series->trailer_url"
                            name="trailer_url" placeholder="https://youtube.com/..." />
                    </div>
                    {{-- TMDB --}}

                </div>

                <div class="div row">
                    <div class="col-md-6">
                        <x-form.input type="number" min="0" label="{{ __('admin.tmdb_id') }}"
                            :value="$series->tmdb_id" name="tmdb_id" placeholder="مثال: 1412" />
                    </div>
                    <div class="col-md-6">
                        <button type="button" id="tmdbSyncBtn" class="btn btn-primary">مزامنة من TMDB</button>
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
                        @php
                            $poster_url = Str::startsWith($series->poster_url, ['http', 'https']);
                            $poster_url_out = $poster_url ? $series->poster_url : null;
                        @endphp
                        <x-form.input type="url" label="{{ __('admin.poster_url') }}" :value="$poster_url_out"
                            name="poster_url_out" placeholder="{{ __('admin.poster_url_placeholder') }}" />
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
                        @php
                            $backdrop_url = Str::startsWith($series->backdrop_url, ['http', 'https']);
                            $backdrop_url_out = $backdrop_url ? $series->backdrop_url : null;
                        @endphp
                        <x-form.input type="url" label="{{ __('admin.backdrop_url') }}" :value="$backdrop_url_out"
                            name="backdrop_url_out" placeholder="{{ __('admin.backdrop_url_placeholder') }}" />
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
                    <button type="button" id="uploadFormBtn"
                        class="btn btn-primary">{{ __('admin.upload_image') }}</button>
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
                <button type="button" class="btn btn-danger"
                    id="confirmDeleteBtn">{{ __('admin.delete') }}</button>
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

    <script>
        // نفس المتغيّرات اللي بتستخدمها عند الأفلام


        let person_duplicate = "{{ __('admin.person_duplicate') }}";
        const form_type = "{{ isset($btn_label) }}";
        const urlPeopleSearch = "{{ route('dashboard.people.search') }}";


        // IMPORTANT: الراوت اللي حكيته أنت
        const seriesCastRowPartial = "{{ route('dashboard.series.castRowPartial') }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/custom/series.js') }}"></script>
@endpush

@push('scripts')
    <script>
        const form_type_ser = {{ isset($btn_label) ? 'true' : 'false' }};

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                minimumResultsForSearch: 0, // إجبار ظهور البحث
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });
        });

        function setImage(url, inputName, imgId, clearBtnId) {
            if (!url) return;

            $(`input[name='${inputName}']`).val(url);
            $(`#${imgId}`).attr("src", url).removeClass("d-none");
            $(`#${clearBtnId}`).removeClass("d-none");
        }
        // -----------------------------
        // TMDB SYNC BUTTON (SERIES)
        // -----------------------------
        $("#tmdbSyncBtn").on("click", function() {
            let id = $("input[name='tmdb_id']").val();

            if (!id) {
                alert("الرجاء إدخال TMDB ID");
                return;
            }

            $.ajax({
                url: `/dashboard/series/tmdb-sync/${id}`,
                method: "GET",
                success: function(res) {
                    if (!res.status) {
                        alert(res.message || "حدث خطأ أثناء المزامنة");
                        return;
                    }

                    const series = res.data;
                    // تعبئة بيانات المسلسل
                    $("input[name='title_ar']").val(series.title_ar);
                    $("input[name='title_en']").val(series.title_en);
                    $("textarea[name='description_ar']").val(series.description_ar);
                    $("textarea[name='description_en']").val(series.description_en);
                    $("input[name='first_air_date']").val(series.first_air_date);
                    $("input[name='last_air_date']").val(series.last_air_date);

                    $("input[name='poster_url_out']").val(series.poster_url_out);
                    $("input[name='poster_url']").val(series.poster_url_out);
                    setImage(series.poster_url_out, 'poster_url', 'poster_img', 'clearImageBtn1');

                    $("input[name='backdrop_url_out']").val(series.backdrop_url_out);
                    $("input[name='backdrop_url']").val(series.backdrop_url_out);
                    setImage(series.backdrop_url_out, 'backdrop_url', 'backdrop_img', 'clearImageBtn2');

                    $("input[name='tmdb_id']").val(series.tmdb_id);
                    $("input[name='trailer_url']").val(series.trailer_url);
                    $("input[name='language']").val(series.language);
                    $("input[name='series_status']").val(series.series_status);
                    $("input[name='episodes_count']").val(series.episodes_count);
                    $("input[name='seasons_count']").val(series.seasons_count);
                    $("input[name='country']").val(series.country);


                    // ======= التصنيفات ========
                    let select = $('#category-select');
                    res.categories.forEach(cat => {
                        if ($('#category-select option[value="' + cat.id + '"]').length === 0) {
                            let newOption = new Option(cat.name, cat.id, false, true);
                            $('#category-select').append(newOption);
                        }
                    });
                    let selectedIds = series.category_ids.map(id => id.toString());
                    select.val(selectedIds).trigger('change');

                    // ----------------------------
                    // CAST
                    // ----------------------------
                    renderCastRows(res.cast);

                    alert("تمت المزامنة بنجاح!");
                },

                error: function() {
                    alert("خطأ في الاتصال بالـ API");
                }
            });
        });

        // ----------------------------
        // Render Cast Rows
        // ----------------------------
        function renderCastRows(cast) {
            let container = $('#cast-rows');
            container.html('');

            cast.forEach((row, i) => {
                $.ajax({
                    url: '/dashboard/series/castRowPartial',
                    method: 'GET',
                    data: {
                        i: i,
                        row: JSON.stringify(row)
                    },
                    success: function(html) {
                        container.append(html);
                    }
                });
            });
        }

        $(document).on("click", "#category-badges label", function() {
            $(this).toggleClass("active");

            let checkbox = $(this).find("input[type='checkbox']");
            checkbox.prop("checked", $(this).hasClass("active"));

            refreshSelectedCategories();
        });

        $(document).on('select2:select', '.person-select', function(e) {
            let data = e.params.data;

            let wrapper = $(this).closest('.cast-row');

            wrapper.find('.person-name-input').val(data.text);
        });
    </script>
@endpush
