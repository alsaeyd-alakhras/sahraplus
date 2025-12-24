<div class="row">
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/custom/movies.css') }}">
    @endpush
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $key => $error)
                    <li>{{ $key . " : " . $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- العناوين --}}

                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.movie_title_ar') }}" :value="$movie->title_ar" name="title_ar"
                            placeholder="{{ __('admin.movie_title_ar_example') }}" required autofocus />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.movie_title_en') }}" :value="$movie->title_en" name="title_en"
                            placeholder="{{ __('admin.title_en_placeholder') }}" />

                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">

                    {{-- الأوصاف --}}
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_ar') }}" name="description_ar" rows="2" :value="$movie->description_ar"
                            placeholder="{{ __('admin.movie_description_placeholder') }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_en') }}" name="description_en" rows="2" :value="$movie->description_en"
                            placeholder="{{ __('admin.movie_description_placeholder') }}" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- تاريخ/مدة/تقييم --}}
                    <div class="mb-4 col-md-4">

                        <x-form.input type="date" label="{{ __('admin.release_date') }}" :value="$movie->release_date?->format('Y-m-d')" name="release_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="{{ __('admin.duration_minutes') }}" :value="$movie->duration_minutes" name="duration_minutes"
                            placeholder="120" min="0" />

                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" step="any" label="{{ __('admin.imdb_rating_label') }}" :value="$movie->imdb_rating"
                            name="imdb_rating" placeholder="7" min="0" max="10" />
                    </div>

                    {{-- التصنيف/اللغة/ الدولة --}}
                    <div class="mb-4 col-md-4">

                        <x-form.selectkey label="{{ __('admin.content_rating_label') }}" name="content_rating" :selected="$movie->content_rating ?? 'G'"
                            :options="$contentRatingOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.language_label') }}" name="language" :selected="$movie->language ?? 'ar'" :options="$languageOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.production_country') }}" name="country" :selected="$movie->country" :options="$countries" />
                    </div>

                    {{-- الحالة --}}
                    <div class="mb-4 col-md-6">
                        <x-form.selectkey label="{{ __('admin.publish_status') }}" name="status" required :selected="$movie->status ?? 'draft'"
                            :options="$statusOptions" />
                    </div>

                    {{-- مميز --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.display_options') }}</label>
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" @checked(old('is_featured', $movie->is_featured))>
                            <label class="form-check-label" for="is_featured">{{ __('admin.show_as_featured') }}</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_kids" value="0">
                            <input class="form-check-input" type="checkbox" id="is_kids" name="is_kids"
                                value="1" @checked(old('is_kids', $movie->is_kids))>
                            <label class="form-check-label" for="is_kids">{{ __('admin.KidsContent') }}</label>
                        </div>


                    </div>



                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="{{ __('admin.intro_skip_time') }}" :value="$movie->intro_skip_time" name="intro_skip_time"
                            min="0" />

                    </div>

                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.logo_url_label') }}" :value="$movie->logo_url" name="logo_url" required
                            placeholder="https://example.com" />
                    </div>


                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الروابط / الرفع: بوستر وخلفية --}}
                    <div class="mb-4 col-md-6">

                        @php
                            $poster_url = Str::startsWith($movie->poster_url, ['http', 'https']);
                            $poster_url_out = $poster_url ? $movie->poster_url : null;
                        @endphp
                        <x-form.input type="url" label="{{ __('admin.poster_url_label') }}" :value="$poster_url_out" name="poster_url_out"
                            placeholder="{{ __('admin.choose_from_media') }}" />

                        <input type="text" id="imageInput" name="poster_url"
                            value="{{ old('poster_url', $movie->poster_url) }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn1" data-img="#poster_img" data-mode="single"
                                data-input="#imageInput" data-out-input="#poster_url_out" class="mt-3 btn btn-primary openMediaModal">
                                {{ __('admin.choose_from_media') }}
                            </button>
                            <button type="button"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($movie->poster_url) ? '' : 'd-none' }}"
                                id="clearImageBtn1" data-img="#poster_img" data-input="#imageInput" data-out-input="#poster_url_out">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ !empty($movie->poster_full_url) ? $movie->poster_full_url : asset('imgs/default.png') }}"
                                alt="poster" id="poster_img"
                                class="{{ !empty($movie->poster_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                    </div>

                    <div class="mb-4 col-md-6">

                        @php
                            $backdrop_url = Str::startsWith($movie->backdrop_url, ['http', 'https']);
                            $backdrop_url_out = $backdrop_url ? $movie->backdrop_url : null;
                        @endphp
                        <x-form.input type="url" label="{{ __('admin.backdrop_url_label') }}" :value="$backdrop_url_out" name="backdrop_url_out"
                            placeholder="{{ __('admin.choose_from_media') }}" />

                        <input type="text" id="imageInput2" name="backdrop_url"
                            value="{{ old('backdrop_url', $movie->backdrop_url) }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn2" data-img="#backdrop_img" data-mode="single"
                                data-input="#imageInput2" data-out-input="#backdrop_url_out" class="mt-3 btn btn-primary openMediaModal">
                                {{ __('admin.choose_from_media') }}
                            </button>
                            <button type="button"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($movie->backdrop_url) ? '' : 'd-none' }}"
                                id="clearImageBtn2" data-img="#backdrop_img" data-input="#imageInput2" data-out-input="#backdrop_url_out">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $movie->backdrop_full_url }}" alt="backdrop" id="backdrop_img"
                                class="{{ !empty($movie->backdrop_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                        <div class="mb-4 col-md-6">
                            <x-form.input type="number" min="0" label="{{ __('admin.view_count') }}" :value="$movie->view_count ?? 0"
                                name="view_count" placeholder="0" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TMDB و عداد المشاهدات (اختياري) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" min="0" label="TMDB ID" :value="$movie->tmdb_id"
                            name="tmdb_id" placeholder="{{ __('admin.example') }}: 550" />

                    </div>
                    <div class="mb-4 col-md-6">
                        <button type="button" id="tmdbSyncBtn" class="btn btn-primary">{{ __('admin.sync_from_tmdb') }}</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🆕 التصنيفات + الأشخاص (Many-to-Many) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- Categories --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">
                            {{ __('admin.Movie Category') }}
                        </label>

                        <select name="category_ids[]"    id="category-select" class="form-control select2" multiple
                            data-placeholder="{{ __('admin.select_categories') }}">
                            @foreach ($allCategories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, old('category_ids', $movie->categories->pluck('id')->toArray() ?? []))
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

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- Cast (movie_cast) --}}
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.Cast') }}</label>
                            <button type="button" id="add-cast-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.Create') }}
                            </button>
                        </div>

                      <div id="cast-rows" class="gap-3 d-grid">
                            @php
                                $oldCast = old(
                                    'cast',
                                    isset($movie)
                                        ? $movie->people
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
                                @include('dashboard.movies.partials._cast_row', [
                                    'i' => $i,
                                    'row' => $row,
                                    'allPeople' => $allPeople ?? collect(),
                                    'roleTypes' => $roleTypes,
                                ])
                            @empty
                                @include('dashboard.movies.partials._cast_row', [
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

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.video_files') }}</label>
                            <button type="button" id="add-video-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.add') }}
                            </button>
                        </div>

                        <div id="video-rows" class="gap-3 d-grid">
                            @php
                                $oldVideos = old(
                                    'video_files',
                                    isset($movie)
                                        ? $movie->videoFiles
                                            ->map(function ($vf) {
                                                return [
                                                    'id' => $vf->id,
                                                    'video_type' => $vf->video_type,
                                                    'quality' => $vf->quality,
                                                    'file_url' => $vf->file_url,
                                                    'format' => $vf->format,
                                                ];
                                            })
                                            ->toArray()
                                        : [],
                                );
                            @endphp

                            @if (empty($oldVideos) && !isset($btn_label))
                                @include('dashboard.movies.partials._video_row', ['i' => 0, 'row' => []])
                            @else
                                @foreach ($oldVideos as $i => $row)
                                    @include('dashboard.movies.partials._video_row', [
                                        'i' => $i,
                                        'row' => $row,
                                    ])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.subtitles') }}</label>
                            <button type="button" id="add-sub-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.add') }}
                            </button>
                        </div>

                        <div id="sub-rows" class="gap-3 d-grid">
                            @php
                                $oldSubs = old(
                                    'subtitles',
                                    isset($movie)
                                        ? $movie->subtitles->map
                                            ->only(['language', 'id', 'label', 'file_url', 'is_default'])
                                            ->toArray()
                                        : [],
                                );
                            @endphp

                            @if (empty($oldSubs) && !isset($btn_label))
                                @include('dashboard.movies.partials._subtitle_row', [
                                    'i' => 0,
                                    'row' => [],
                                ])
                            @else
                                @foreach ($oldSubs as $i => $row)
                                    @include('dashboard.movies.partials._subtitle_row', [
                                        'i' => $i,
                                        'row' => $row,
                                    ])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                {{ $btn_label ?? 'أضف' }}
            </button>
        </div>
    </div>
</div>

@include('layouts.partials.dashboard.mediamodel')


@push('scripts')
    <script>
        let person_duplicate = "{{ __('admin.person_duplicate') }}";
        const form_type = "{{ isset($btn_label) }}";
        const castRowPartial = "{{ route('dashboard.movies.castRowPartial') }}";
        const videoRowPartial = "{{ route('dashboard.movies.videoRowPartial') }}";
        const subtitleRowPartial = "{{ route('dashboard.movies.subtitleRowPartial') }}";
        var urlPeopleSearch = "{{ route('dashboard.people.search') }}";

    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/custom/movies.js') }}"></script>
@endpush

@push('scripts')
    <script>
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

        // ================================
        // زر الـ TMDB SYNC
        // ================================
        $("#tmdbSyncBtn").on("click", function() {
            let id = $("input[name='tmdb_id']").val();

            if (!id) {
                alert("الرجاء إدخال TMDB ID");
                return;
            }

            $.ajax({
                url: `/dashboard/movies/tmdb-sync/${id}`,
                method: "GET",
                success: function(res) {
                    if (!res.status) {
                        alert(res.message || "حدث خطأ أثناء المزامنة");
                        return;
                    }

                    const movie = res.data;

                    // تعبئة الفورم
                    $("input[name='title_ar']").val(movie.title_ar);
                    $("input[name='title_en']").val(movie.title_en);
                    $("textarea[name='description_ar']").val(movie.description_ar);
                    $("textarea[name='description_en']").val(movie.description_en);
                    $("input[name='release_date']").val(movie.release_date);
                    $("input[name='duration_minutes']").val(movie.duration_minutes);
                    $("input[name='imdb_rating']").val(movie.imdb_rating);

                    // بوستر
                    $("input[name='poster_url_out']").val(movie.poster_url_out);
                    $("input[name='poster_url']").val(movie.poster_url_out);
                    setImage(movie.poster_url_out, 'poster_url', 'poster_img', 'clearImageBtn1');
                    $("input[name='backdrop_url_out']").val(movie.backdrop_url_out);
                    setImage(movie.backdrop_url_out, 'backdrop_url', 'backdrop_img', 'clearImageBtn2');
                    $("input[name='tmdb_id']").val(movie.tmdb_id);
                    // $("input[name='view_count']").val(movie.view_count);
                    $("input[name='logo_url']").val(movie.logo_url);
                    $("input[name='intro_skip_time']").val(movie.intro_skip_time);

                    // ======= التصنيفات ========
                    let select = $('#category-select');
                    res.categories.forEach(cat => {
                        if ($('#category-select option[value="' + cat.id + '"]').length === 0) {
                            let newOption = new Option(cat.name, cat.id, false, true);
                            $('#category-select').append(newOption);
                        }
                    });
                    let selectedIds = movie.category_ids.map(id => id.toString());
                    select.val(selectedIds).trigger('change');

                    renderCastRows(res.cast);

                    alert("تمت المزامنة بنجاح!");
                },

                error: function() {
                    alert("خطأ في الاتصال بالـ API");
                }
            });
        });

        function renderCastRows(cast) {
            let container = $('#cast-rows');
            container.html('');

            cast.forEach((row, i) => {
                $.ajax({
                    url: '/dashboard/movies/castRowPartial',
                    method: 'GET',
                    data: {
                        i: i,
                        row: JSON.stringify(row) // الحل هنا
                    },
                    success: function(html) {
                        container.append(html);
                    }
                });
            });
        }

        $(document).on('select2:select', '.person-select', function(e) {
            let data = e.params.data;

            let wrapper = $(this).closest('.cast-row');

            wrapper.find('.person-name-input').val(data.text);
        });
    </script>
@endpush
