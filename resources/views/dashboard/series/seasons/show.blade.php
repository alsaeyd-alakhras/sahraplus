<x-dashboard-layout>
    @php
        $title = 'title_' . app()->getLocale();
        $description = 'description_' . app()->getLocale();
    @endphp
    @push('styles')
        <!-- Page CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
    @endpush
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="mt-12 mb-6 card">
                <div class="mb-5 text-center user-profile-header d-flex flex-column flex-lg-row text-sm-start">
                    @if ($season->poster_full_url)
                        <div class="flex-shrink-0 mx-auto mt-n2 mx-sm-0">
                            <img src="{{ $season->poster_full_url }}" alt="user image"
                                class="h-auto rounded d-block ms-0 ms-sm-6 user-profile-img" />
                        </div>
                    @endif
                    <div class="mt-3 flex-grow-1 mt-lg-5">
                        <div
                            class="gap-4 mx-5 d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start flex-md-row flex-column">
                            <div class="user-profile-info">
                                <h4 class="mb-2 mt-lg-6">{{ $season->$title }}</h4>
                                <p class="text-muted">{{ $season->$description }}</p>
                            </div>
                            <button class="mb-1 btn btn-primary" id="editSeasonBtn"
                                data-season-id="{{ $season->id }}">
                                <i class="ti ti-edit ti-xs me-2"></i> {{ __('admin.edit_data') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <div class="row">
        <!-- Season Details -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card">
                <div class="card-body">
                    <small class="mb-3 card-text text-uppercase text-muted small">
                        {{ __('admin.season_details') }}
                    </small>
                    <ul class="py-1 list-unstyled">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-hash ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.season_number') }}:</span>
                            <span>{{ $season->season_number }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-movie ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.title_ar') }}:</span>
                            <span>{{ $season->title_ar }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-language ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.title_en') }}:</span>
                            <span>{{ $season->title_en }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-calendar ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.air_date') }}:</span>
                            <span>{{ $season->air_date }}</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="ti ti-align-left ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.description_ar') }}:</span>
                            <span>{{ Str::limit($season->description_ar, 100) }}</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="ti ti-align-left ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.description_en') }}:</span>
                            <span>{{ Str::limit($season->description_en, 100) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="mb-6 card">
                <div class="card-body">
                    <small class="card-text text-uppercase text-muted small">
                        {{ __('admin.season_statistics') }}
                    </small>
                    <ul class="pt-1 mt-3 mb-0 list-unstyled">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-video ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.episode_count') }}:</span>
                            <span>{{ $season->episodes()->count() }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-eye ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.view_count') }}:</span>
                            <span>{{ $season->view_count ?? 0 }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-circle-check ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.status') }}:</span>
                            <span>{{ $season->status }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 app-academy">
        <div class="mb-6 card">
            <div class="flex-wrap gap-4 card-header d-flex justify-content-between">
                <div class="mb-0 card-title me-1">
                    <h5 class="mb-0">{{ __('admin.episodes') }}</h5>
                    <p class="mb-0">{{ __('admin.total_episodes') }} : {{ $season->episodes()->count() }}</p>
                </div>
                <div class="d-flex justify-content-md-end align-items-center column-gap-6">
                    {{-- <select class="form-select">
                        <option value="">All Courses</option>
                        <option value="ui/ux">UI/UX</option>
                        <option value="seo">SEO</option>
                        <option value="web">Web</option>
                        <option value="music">Music</option>
                        <option value="painting">Painting</option>
                    </select> --}}

                    <div class="my-2 form-check form-switch ms-2">
                        <a href="{{ route('dashboard.episodes.create',['season_id'=>$season->id]) }}" class="btn btn-primary">
                            <i class="ti ti-plus ti-lg"></i>
                            {{ __('admin.add_episode') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-6 row gy-6" id="episodes-container">
                    @foreach ($season->episodes as $episode)
                        <div class="col-sm-6 col-lg-4" id="season-{{ $episode->id }}">
                            <div class="p-2 border shadow-none card h-100">
                                <div class="mb-4 text-center rounded-2">
                                    <img class="img-fluid"
                                        src="{{ $episode->thumbnail_full_url ? $episode->thumbnail_full_url : asset('assets/img/pages/profile-banner.png') }}"
                                        alt="tutor image 1" />
                                </div>
                                <div class="p-4 pt-2 card-body">
                                    <a href="#" class="h5">{{ $episode->title }}</a>
                                    <p class="mt-1">{{ $episode->description }}</p>
                                    <p class="mb-1 d-flex align-items-center">
                                        <i class="ti ti-time ti-lg"></i>
                                        <span class="mx-2 fw-medium">{{ __('admin.duration_minutes') }}:</span>
                                        <span>{{ $episode->duration_minutes }}</span>
                                    </p>
                                    <div
                                        class="flex-wrap gap-4 d-flex flex-column flex-md-row text-nowrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                                        <a class="w-100 btn btn-label-secondary d-flex align-items-center"
                                            href="{{ route('dashboard.episodes.edit', $episode->id) }}">
                                            <i class="align-middle ti ti-edit ti-xs scaleX-n1-rtl me-2"></i>
                                            <span>{{ __('admin.edit_data') }}</span>
                                        </a>
                                        {{-- <a class="w-100 btn btn-label-primary d-flex align-items-center"
                                            href="{{ route('dashboard.episodes.show', $episode->id) }}">
                                            <span class="me-2">{{ __('admin.manage') }}</span><i
                                                class="ti ti-chevron-right ti-xs scaleX-n1-rtl"></i>
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="seasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seasonModalTitle">{{ __('admin.add_season') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSeasonForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-4 col-md-6">
                                <x-form.input label="{{ __('admin.season_number') }}" name="season_number"
                                    type="number" placeholder="1" required autofocus />
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.input label="{{ __('admin.air_date') }}" name="air_date" type="date" />
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.input label="{{ __('admin.title_ar') }}" name="title_ar"
                                    placeholder="عنوان الموسم بالعربية" />
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.input label="{{ __('admin.title_en') }}" name="title_en"
                                    placeholder="Season Title (EN)" />
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.textarea label="{{ __('admin.description_ar') }}" name="description_ar"
                                    rows="2" placeholder="وصف الموسم بالعربية..." />
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.textarea label="{{ __('admin.description_en') }}" name="description_en"
                                    rows="2" placeholder="Season description in English..." />
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.selectkey label="{{ __('admin.status') }}" name="status"
                                    :options="[
                                        'draft' => __('admin.draft'),
                                        'published' => __('admin.published'),
                                        'archived' => __('admin.archived'),
                                    ]" />
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.input label="{{ __('admin.tmdb_id') }}" name="tmdb_id"
                                    placeholder="TMDB ID" />
                            </div>

                            <div class="mb-4 col-md-6">
                                <input type="text" id="posterInput" name="poster_url" value=""
                                    class="d-none form-control">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                        data-clear-btn="#clearPosterBtn" data-img="#poster_img" data-mode="single"
                                        data-model-next="#seasonModal" data-input="#posterInput"
                                        class="mt-3 btn btn-primary openMediaModal">
                                        {{ __('admin.poster_img_choose') }}
                                    </button>
                                    <button type="button" id="clearPosterBtn"
                                        class="mt-3 clear-btn btn btn-danger d-none" data-img="#poster_img"
                                        data-input="#posterInput">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <img src="" alt="poster" id="poster_img" class="d-none"
                                        style="max-height:100px">
                                </div>
                            </div>

                            <x-form.input type="hidden" name="episode_count" min="0" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            {{ __('admin.close') }}
                        </button>
                        <button type="button" id="saveSeasonBtn"
                            class="btn btn-primary">{{ __('admin.save') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    {{-- مودال الوسائط --}}
    <div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-6 text-2xl font-bold modal-title">📁 مكتبة الوسائط</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeMediaModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4 modal-body">
                    <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="file" name="image" id="imageInputMedia" class="mb-2 form-control">
                        <button type="button" id="uploadFormBtn" class="btn btn-primary">رفع صورة</button>
                    </form>
                    <div id="mediaGrid" class="masonry">
                        {{-- الصور ستُملأ تلقائيًا عبر jQuery --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="selectMediaBtn">اختيار</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeDeleteModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد من حذف هذه الصورة؟
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
        <!-- Page JS -->
        <script src="{{ asset('assets/js/pages-profile.js') }}"></script>
        <script>
            $(document).ready(function() {
                let modeForm = 'create';
                let seasonId = null;
                $(document).on('click', '#editSeasonBtn', function() {
                    modeForm = 'edit';
                    $('#seasonModal').modal('show');
                    getEditSeason($(this).data('season-id'));
                });

                function getEditSeason(id) {
                    seasonId = id;
                    $.ajax({
                        url: "{{ route('dashboard.seasons.edit', ':id') }}".replace(':id', seasonId),
                        method: "GET",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: seasonId,
                        },
                        success: function(response) {
                            Object.entries(response).forEach(([key, value]) => {
                                if (key == 'poster_full_url') {
                                    $('#poster_img').attr('src', value);
                                    $('#poster_img').removeClass('d-none');
                                }
                                let $el = $('#' + key);
                                if (!$el.length) $el = $('[name="' + key + '"]');
                                if (!$el.length) return;

                                const type = ($el.attr('type') || '').toLowerCase();

                                if (type === 'date' || key.endsWith('_date')) {
                                    if (typeof value === 'string') value = value.slice(0, 10);
                                }
                                if (type === 'checkbox') return $el.prop('checked', !!value);
                                if ($el.is('select')) return $el.val(value).trigger('change');
                                if (type === 'file') return;

                                $el.val(value);
                            });

                            $('#seasonModalTitle').text("{{ __('admin.edit_season') }}");
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error:", status, error);
                            toastr.error("هنالك خطاء في عملية الحذف.");
                        },
                    });
                }
                $('#saveSeasonBtn').click(function() {
                    if (modeForm == 'edit') {
                        $.ajax({
                            url: "{{ route('dashboard.seasons.update', ':id') }}".replace(':id',
                                seasonId),
                            method: "PUT",
                            data: $('#addSeasonForm').serialize(),
                            success: function(response) {
                                window.location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX error:", status, error);
                                toastr.error("هنالك خطاء في عملية الحذف.");
                            },
                        });
                    }
                });
            });
        </script>
    @endpush

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
</x-dashboard-layout>
