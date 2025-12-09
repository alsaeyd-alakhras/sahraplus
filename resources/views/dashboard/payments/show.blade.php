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
            <div class="mb-6 card">

                <div class="mb-5 text-center user-profile-header d-flex flex-column flex-lg-row text-sm-start">

                    <div class="mt-3 flex-grow-1 mt-lg-5">
                        <div
                            class="gap-4 mx-5 d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start flex-md-row flex-column">
                            <div class="user-profile-info">
                                <h4 class="mb-2 mt-lg-6">{{ @$pay->user->first_name .' '. @$pay->user->last_name }}</h4>
                                <p class="text-muted">{{ @$pay->user->email }}</p>
                                 <h5 class="text-muted">{{ @$pay->subscription->plan->name_ar }}</h5>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <div class="row">
        <!-- Series Details -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card">
                <div class="card-body">
                    <small
                        class="mb-3 card-text text-uppercase text-muted small">{{ __('admin.series_details') }}</small>
                    <ul class="py-1 list-unstyled">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-movie ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.payment_reference') }}:</span>
                            <span>{{ $pay->payment_reference }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-language ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.amount') }}:</span>
                            <span>{{ $pay->amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.currency') }}:</span>
                            <span>{{ $pay->currency }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.tax_amount') }}:</span>
                            <span>{{ $pay->tax_amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.fee_amount') }}:</span>
                            <span>{{ $pay->fee_amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.net_amount') }}:</span>
                            <span>{{ $pay->net_amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.payment_method') }}:</span>
                            <span>{{ $pay->payment_method }}</span>
                        </li>

                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.gateway') }}:</span>
                            <span>{{ $pay->gateway }}</span>
                        </li>
                        

                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.gateway_transaction_id') }}:</span>
                            <span>{{ $pay->gateway_transaction_id }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.status') }}:</span>
                            <span>{{ $pay->status }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.gateway_response') }}:</span>
                            <span>{{ $pay->gateway_response }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.processed_at') }}:</span>
                            <span>{{ $pay->processed_at }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.failed_at') }}:</span>
                            <span>{{ $pay->failed_at }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.refunded_at') }}:</span>
                            <span>{{ $pay->refunded_at }}</span>
                        </li>

                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.created_at') }}:</span>
                            <span>{{ $pay->created_at }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.updated_at') }}:</span>
                            <span>{{ $pay->updated_at }}</span>
                        </li>

                    </ul>
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


                $('#addSeasonBtn').click(function() {
                    modeForm = 'create';
                    seasonId = null;

                    // فرغ جميع الحقول
                    $('#addSeasonForm')[0].reset(); // يفرغ كل input و textarea و select
                    $('#poster_img').attr('src', '').addClass('d-none'); // يفرغ الصورة
                    $('#posterInput').val(''); // يفرغ input البوستر
                    $('#clearPosterBtn').addClass('d-none'); // يخفي زر الحذف
                    $('#seasonModalTitle').text("{{ __('admin.add_season') }}"); // عنوان المودال

                    // افتح المودال
                    $('#seasonModal').modal('show');
                });


                function getNewSeason() {
                    $.ajax({
                        url: "{{ route('dashboard.seasons.create') }}",
                        method: "GET",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            Object.entries(response).forEach(([key, value]) => {
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
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error:", status, error);
                            toastr.error("هنالك خطاء في عملية الحذف.");
                        },
                    });
                }
                $(document).on('click', '.editSeasonBtn', function() {
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
                    if (modeForm == 'create') {
                        $.ajax({
                            url: "{{ route('dashboard.seasons.store') }}",
                            method: "POST",
                            data: $('#addSeasonForm').serialize(),
                            success: function(response) {
                                let html = `
                                <div class="col-sm-6 col-lg-4" id="season-${response.id}">
                                    <div class="p-2 border shadow-none card h-100">
                                        <div class="mb-4 text-center rounded-2">
                                            <img class="img-fluid"
                                                src="${response.poster_full_url ? response.poster_full_url : '{{ asset('assets/img/pages/profile-banner.png') }}'}"
                                                alt="tutor image 1" />
                                        </div>
                                        <div class="p-4 pt-2 card-body">
                                            <a href="#" class="h5">${response.title}</a>
                                            <p class="mt-1">${response.description}</p>
                                            <p class="mb-1 d-flex align-items-center">
                                                <i class="ti ti-video ti-lg"></i>
                                                <span class="mx-2 fw-medium">{{ __('admin.episodes_count') }}:</span>
                                                <span>0</span>
                                            </p>
                                            <div
                                                class="flex-wrap gap-4 d-flex flex-column flex-md-row text-nowrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                                                <button class="w-100 btn btn-label-secondary d-flex align-items-center editSeasonBtn"
                                                    id="editSeasonBtn-${response.id}" data-season-id="${response.id}">
                                                    <i class="align-middle ti ti-edit ti-xs scaleX-n1-rtl me-2"></i>
                                                    <span>{{ __('admin.edit_data') }}</span>
                                                </button>
                                                <a class="w-100 btn btn-label-primary d-flex align-items-center" href="{{ route('dashboard.seasons.show', ':id') }}">
                                                    <span class="me-2">{{ __('admin.manage') }}</span><i
                                                        class="ti ti-chevron-right ti-xs scaleX-n1-rtl"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                                html = html.replace(':id', response.id);
                                $('#seasons-container').append(html);
                                toastr.success("تم إضافة الموسم بنجاح.");
                                $('#seasonModal').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX error:", status, error);
                                toastr.error("هنالك خطاء في عملية الإضافة.");
                            },
                        });
                    }
                    if (modeForm == 'edit') {
                        $.ajax({
                            url: "{{ route('dashboard.seasons.update', ':id') }}".replace(':id',
                                seasonId),
                            method: "PUT",
                            data: $('#addSeasonForm').serialize(),
                            success: function(response) {
                                let html = `
                                <div class="col-sm-6 col-lg-4" id="season-${response.id}">
                                    <div class="p-2 border shadow-none card h-100">
                                        <div class="mb-4 text-center rounded-2">
                                            <img class="img-fluid"
                                                src="${response.poster_full_url ? response.poster_full_url : '{{ asset('assets/img/pages/profile-banner.png') }}'}"
                                                alt="tutor image 1" />
                                        </div>
                                        <div class="p-4 pt-2 card-body">
                                            <a href="#" class="h5">${response.title}</a>
                                            <p class="mt-1">${response.description}</p>
                                            <p class="mb-1 d-flex align-items-center">
                                                <i class="ti ti-video ti-lg"></i>
                                                <span class="mx-2 fw-medium">{{ __('admin.episodes_count') }}:</span>
                                                <span>0</span>
                                            </p>
                                            <div
                                                class="flex-wrap gap-4 d-flex flex-column flex-md-row text-nowrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                                                <button class="w-100 btn btn-label-secondary d-flex align-items-center editSeasonBtn"
                                                    id="editSeasonBtn-${response.id}" data-season-id="${response.id}">
                                                    <i class="align-middle ti ti-edit ti-xs scaleX-n1-rtl me-2"></i>
                                                    <span>{{ __('admin.edit_data') }}</span>
                                                </button>
                                                <a class="w-100 btn btn-label-primary d-flex align-items-center" href="{{ route('dashboard.seasons.show', ':id') }}">
                                                    <span class="me-2">{{ __('admin.manage') }}</span><i
                                                        class="ti ti-chevron-right ti-xs scaleX-n1-rtl"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                                html = html.replace(':id', response.id);
                                $('#seasons-container').find('#season-' + response.id).replaceWith(
                                    html);
                                toastr.success("تم تعديل الموسم بنجاح.");
                                $('#seasonModal').modal('hide');
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
