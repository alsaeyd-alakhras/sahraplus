<div class="row">
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/movies.css') }}">
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
                        <x-form.input label="عنوان الفيلم (عربي)" :value="$movie->title_ar" name="title_ar"
                            placeholder="مثال: الطريق إلى القدس" required autofocus />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="عنوان الفيلم (إنجليزي)" :value="$movie->title_en" name="title_en"
                            placeholder="Movie Title (EN)" />

                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">

                    {{-- الأوصاف --}}
                    <div class="col-md-6">
                        <x-form.textarea label="الوصف (عربي)" name="description_ar" rows="2" :value="$movie->description_ar"
                            placeholder="نبذة عن الفيلم..." />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label="الوصف (En)" name="description_en" rows="2" :value="$movie->description_en"
                            placeholder="نبذة عن الفيلم..." />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- تاريخ/مدة/تقييم --}}
                    <div class="mb-4 col-md-4">

                        <x-form.input type="date" label="تاريخ الإصدار" :value="$movie->release_date?->format('Y-m-d')" name="release_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="المدة بالدقائق" :value="$movie->duration_minutes" name="duration_minutes"
                            placeholder="120" min="0" />

                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" step="0.1" label="تقييم IMDb (0-10)" :value="$movie->imdb_rating"
                            name="imdb_rating" placeholder="7.8" min="0" max="10" />
                    </div>

                    {{-- التصنيف/اللغة/الدولة --}}
                    <div class="mb-4 col-md-4">

                        <x-form.selectkey label="التصنيف العمري" name="content_rating" :selected="$movie->content_rating ?? 'G'"
                            :options="$contentRatingOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="اللغة" name="language" :selected="$movie->language ?? 'ar'" :options="$languageOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="بلد الإنتاج" name="country" :selected="$movie->country" :options="$countries" />
                    </div>

                    {{-- الحالة --}}
                    <div class="mb-4 col-md-6">
                        <x-form.selectkey label="حالة النشر" name="status" required :selected="$movie->status ?? 'draft'"
                            :options="$statusOptions" />
                    </div>

                    {{-- مميز --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">مميز</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" @checked(old('is_featured', $movie->is_featured))>
                            <label class="form-check-label" for="is_featured">عرض كفيلم مميز</label>
                        </div>
                    </div>



                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="وقت تخطي المقدمة " :value="$movie->intro_skip_time" name="intro_skip_time"
                            min="0" />

                    </div>

                    <div class="col-md-6">
                        <x-form.input label="Logo Url" :value="$movie->logo_url" name="logo_url"
                            placeholder="https://example.com" />
                    </div>


                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الروابط/الرفع: بوستر وخلفية --}}
                    <div class="mb-4 col-md-6">

                        @php
                            $poster_url = Str::startsWith($movie->poster_url, ['http', 'https']);
                            $poster_url_out = $poster_url ? $movie->poster_url : null;
                        @endphp
                        <x-form.input type="url" label="رابط البوستر" :value="$poster_url_out" name="poster_url_out"
                            placeholder="أو اختر من الوسائط" />

                        <input type="text" id="imageInput" name="poster_url"
                            value="{{ old('poster_url', $movie->poster_url) }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn1" data-img="#poster_img" data-mode="single"
                                data-input="#imageInput" class="mt-3 btn btn-primary openMediaModal">
                                اختر من الوسائط
                            </button>
                            <button type="button"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($movie->poster_url) ? '' : 'd-none' }}"
                                id="clearImageBtn1" data-img="#poster_img" data-input="#imageInput">
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
                        <x-form.input type="url" label="رابط الخلفية" :value="$backdrop_url_out" name="backdrop_url_out"
                            placeholder="أو اختر من الوسائط" />

                        <input type="text" id="imageInput2" name="backdrop_url"
                            value="{{ old('backdrop_url', $movie->backdrop_url) }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn2" data-img="#backdrop_img" data-mode="single"
                                data-input="#imageInput2" class="mt-3 btn btn-primary openMediaModal">
                                اختر من الوسائط
                            </button>
                            <button type="button"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($movie->backdrop_url) ? '' : 'd-none' }}"
                                id="clearImageBtn2" data-img="#backdrop_img" data-input="#imageInput2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $movie->backdrop_full_url }}" alt="backdrop" id="backdrop_img"
                                class="{{ !empty($movie->backdrop_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                        <div class="mb-4 col-md-6">
                            <x-form.input type="number" min="0" label="عدد المشاهدات" :value="$movie->view_count ?? 0"
                                name="view_count" placeholder="0" readonly />
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
                        <x-form.input type="number" min="0" label="TMDB ID" :value="$movie->tmdb_id"
                            name="tmdb_id" placeholder="مثال: 550" />

                    </div>
                    <div class="mb-4 col-md-6">
                        <button type="button" id="tmdbSyncBtn" class="btn btn-primary">مزامنة من TMDB</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🆕 التصنيفات + الأشخاص (Many-to-Many) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- Categories (movie_category_mapping) --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">{{ __('admin.Movie Category') }}</label>

                        {{-- الحاوية للمختارة --}}
                        <div id="selected-categories" class="mb-2 d-none">
                            <div class="flex-wrap gap-2 d-flex"></div>
                            <hr class="mt-2 mb-3">
                        </div>
                        {{-- مهم: لو ما في اختيار، هالحقل يرسل قيمة فاضية بدل ما يختفي المفتاح --}}
                        <input type="hidden" name="category_ids" value="">
                        {{-- الحاوية للكل --}}

                        <div id="category-badges" class="flex-wrap gap-2 d-flex">
                            @foreach ($allCategories as $category)
                                <label class="px-3 py-1 mb-2 btn btn-outline-primary rounded-pill"
                                    data-id="{{ $category->id }}">
                                    <input type="checkbox" class="d-none" name="category_ids[]"
                                        value="{{ $category->id }}"
                                        {{ in_array($category->id, old('category_ids', $movie->categories->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                    {{ $category->name_ar }}
                                </label>
                            @endforeach
                        </div>


                        <span class="text-muted">{{ __('admin.select_at_least_one_category') }}</span>
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

                        {{-- المختار حالياً --}}
                        <div id="cast-selected" class="mb-2 d-none">
                            <div class="flex-wrap gap-2 d-flex"></div>
                            <hr class="mt-2 mb-3">
                        </div>

                        {{-- صفوف التحرير --}}
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
                    <button type="button" id="uploadFormBtn"
                        class="btn btn-primary">{{ __('admin.upload') }}</button>
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
                <h5 class="modal-title">{{ __('admin.Delete Confirmation') }}</h5>
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

                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">نعم، حذف</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let person_duplicate = "{{ __('admin.person_duplicate') }}";
        const form_type = "{{ isset($btn_label) }}";
        const urlPeopleSearch = "{{ route('dashboard.people.search') }}";
        const castRowPartial = "{{ route('dashboard.movies.castRowPartial') }}";
        const videoRowPartial = "{{ route('dashboard.movies.videoRowPartial') }}";
        const subtitleRowPartial = "{{ route('dashboard.movies.subtitleRowPartial') }}";

        // media
        const urlIndex = "{{ route('dashboard.media.index') }}";
        const urlStore = "{{ route('dashboard.media.store') }}";
        const urlDelete = "{{ route('dashboard.media.destroy', ':id') }}";
        const _token = "{{ csrf_token() }}";
        const urlAssetPath = "{{ config('app.asset_url') }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/custom/mediaPage.js') }}"></script>
    <script src="{{ asset('js/custom/movies.js') }}"></script>
@endpush

@push('scripts')
    <script>
        function refreshSelectedCategories() {
            let selectedBox = $("#selected-categories");
            let selectedContainer = $("#selected-categories .d-flex");

            selectedContainer.empty();

            $("#category-badges label.active").each(function() {
                selectedContainer.append(`
            <span class="badge bg-primary px-3 py-2 rounded-pill">
                ${$(this).text().trim()}
            </span>
        `);
            });

            if ($("#category-badges label.active").length > 0) {
                selectedBox.removeClass("d-none");
            } else {
                selectedBox.addClass("d-none");
            }
        }

        // ================================
        // عند الضغط على التصنيف
        // ================================
        $(document).on("click", "#category-badges label", function() {
            $(this).toggleClass("active");

            let checkbox = $(this).find("input[type='checkbox']");
            checkbox.prop("checked", $(this).hasClass("active"));

            refreshSelectedCategories();
        });


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
                    $("input[name='poster_url_out']").val(movie.poster_url_out);
                    $("input[name='backdrop_url_out']").val(movie.backdrop_url_out);
                    $("input[name='tmdb_id']").val(movie.tmdb_id);
                    $("input[name='view_count']").val(movie.view_count);
                    $("input[name='logo_url']").val(movie.logo_url);
                    $("input[name='intro_skip_time']").val(movie.intro_skip_time);

                    // ======= التصنيفات ========

                    const container = $("#category-badges");

                    const existingCategories = [];
                    $("#category-badges label").each(function() {
                        const id = $(this).data("id");
                        const name = $(this).text().trim();
                        existingCategories.push({
                            id,
                            name
                        });
                    });

                    const allCategories = [...existingCategories];

                    // إضافة الجديدة القادمة من TMDB
                    res.categories.forEach(cat => {
                        if (!allCategories.some(c => Number(c.id) === Number(cat.id))) {
                            allCategories.push(cat);
                        }
                    });

                    container.empty();

                    const selectedIds = (movie.category_ids || []).map(id => Number(id));

                    allCategories.forEach(cat => {
                        const isActive = selectedIds.includes(Number(cat.id));

                        container.append(`
                    <label class="px-3 py-1 mb-2 btn btn-outline-primary rounded-pill ${isActive ? "active" : ""}" data-id="${cat.id}">
                        <input type="checkbox" class="d-none" name="category_ids[]" value="${cat.id}" ${isActive ? "checked" : ""}>
                        ${cat.name}
                    </label>
                `);
                    });

                    // تحديث المختارة فوق
                    refreshSelectedCategories();

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
    </script>
@endpush
