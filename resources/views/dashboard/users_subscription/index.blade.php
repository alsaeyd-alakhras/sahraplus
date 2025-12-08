<x-dashboard-layout>
    @push('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{ asset('css/datatable/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable/dataTables.dataTables.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.css') }}">

        {{-- sticky table --}}
        <link id="stickyTableLight" rel="stylesheet" href="{{ asset('css/custom/stickyTable.css') }}">

        {{-- custom css --}}
        <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex2.css') }}">
        <style>
            .btn-icon {
                padding: 5px !important;
            }

            .btn-success {
                color: #fff !important;
                background-color: #28c76f !important;
                border-color: #28c76f !important;
            }
        </style>
    @endpush

    <x-slot:extra_nav>
        <div class="nav-item">
            <select class="form-control" name="advanced-pagination" id="advanced-pagination">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="100">100</option>
                <option value="500">500</option>
                <option value="-1">all</option>
            </select>
        </div>

        <div class="mx-2 nav-item">
            <button class="p-2 border-0 btn btn-outline-danger rounded-pill me-n1 waves-effect waves-light d-none"
                type="button" id="filterBtnClear" title="إزالة التصفية">
                <i class="fa-solid fa-eraser fe-16"></i>
            </button>
        </div>
        <div class="mx-2 nav-item d-flex align-items-center justify-content-center">
            <button type="button" class="btn" id="refreshData">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </x-slot:extra_nav>

    @php
        $fields = [
            'user_id' => __('admin.user_id'),
            'status' => __('admin.status'),
            'amount' => __('admin.amount'),
            'total_amount' => __('admin.total_amount'),
            'currency' => __('admin.currency'),
            'starts_at' => __('admin.starts_at'),
            'ends_at' => __('admin.ends_at'),
            'plan_id' => __('admin.sub_plan'),
        ];
    @endphp

    <div class="shadow-lg enhanced-card">
        <div class="table-header-title">
            <i class="icon ph ph-lock-key me-2"></i>
            {{ __('admin.users_subscription') }}
        </div>
        <div class="enhanced-card-body">
            <div class="col-12" style="padding: 0;">
                <div class="table-container">
                    <table id="movie-categories-table" class="table enhanced-sticky table-striped table-hover"
                        style="display: table; width:100%; height: auto;">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                @foreach ($fields as $index => $label)
                                    <th>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>{{ $label }}</span>
                                            <div class="enhanced-filter-dropdown">
                                                <div class="dropdown">
                                                    <button class="enhanced-btn-filter btn-filter" type="button"
                                                        data-bs-toggle="dropdown"
                                                        id="btn-filter-{{ $loop->index + 1 }}">
                                                        <i class="fas fa-filter"></i>
                                                    </button>
                                                    <div class="dropdown-menu enhanced-filter-menu filterDropdownMenu"
                                                        aria-labelledby="{{ $index }}_filter">
                                                        <div
                                                            class="mb-3 d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox"
                                                                placeholder="ابحث..."
                                                                data-index="{{ $loop->index + 1 }}">
                                                            <button
                                                                class="enhanced-apply-btn ms-2 filter-apply-btn-checkbox"
                                                                data-target="{{ $loop->index + 1 }}"
                                                                data-field="{{ $index }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="enhanced-checkbox-list checkbox-list-box">
                                                            <label style="display:block;">
                                                                <input type="checkbox" value="all"
                                                                    class="all-checkbox"
                                                                    data-index="{{ $loop->index + 1 }}">{{ __('admin.All') }}
                                                            </label>
                                                            <div
                                                                class="checkbox-list checkbox-list-{{ $loop->index + 1 }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                @endforeach
                                {{-- <th class="enhanced-sticky">{{ __('admin.Action') }}</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade delete-modal" id="deleteConfirmModal" tabindex="-1"
        aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel"><i
                            class="fas fa-exclamation-triangle me-2"></i> {{ __('admin.Delete Confirmation') }} </h5>
                </div>
                <div class="modal-body">
                    <div class="delete-icon"><i class="fas fa-trash-alt"></i></div>
                    <div class="delete-warning-text">{{ __('admin.Are you sure?') }}</div>
                    <p class="delete-sub-text"> {{ __('admin.You will not be able to revert this!') }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i
                            class="fas fa-times me-2"></i>إلغاء</button>
                    <button type="button" class="text-white btn btn-confirm-delete" id="confirmDeleteBtn"><i
                            class="fas fa-trash me-2"></i>حذف نهائي</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- DataTables JS -->
        <script src="{{ asset('js/plugins/datatable/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/dataTables.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/dataTables.buttons.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/buttons.dataTables.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/jszip.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/pdfmake.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/vfs_fonts.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatable/buttons.print.min.js') }}"></script>
        <script src="{{ asset('js/plugins/jquery.validate.min.js') }}"></script>

        <script>
            const tableId = 'movie-categories-table';
            const arabicFileJson = "{{ asset('files/Arabic.json') }}";
            const pageLength = $('#advanced-pagination').val();

            // urls
            const _token = "{{ csrf_token() }}";
            const urlIndex = `{{ route('dashboard.users_subscription.index') }}`;
            const urlFilters = `{{ route('dashboard.users_subscription.filters', ':column') }}`; // ← صححنا اسم الراوت
            const urlShow = '{{ route('dashboard.users_subscription.show', ':id') }}';
            const abilityShow = "{{ Auth::guard('admin')->user()->can('show', 'App\\Models\\UserSubscription') }}";

            // أسماء الحقول للفلترة في الهيدر
            const fields = ['#', 'user_id', 'status', 'amount', 'total_amount', 'currency',
                'starts_at', 'ends_at', 'plan_id'
            ];

            // أعمدة الداتا تيبل: نستخدم status_label الراجعة من السيرفس
            const columnsTable = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    class: 'text-center'
                },
                {
                    data: 'user_id',
                    name: 'user_id',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'amount',
                    name: 'amount',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'total_amount',
                    name: 'total_amount',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'currency',
                    name: 'currency',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'starts_at',
                    name: 'starts_at',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'ends_at',
                    name: 'ends_at',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'plan_id',
                    name: 'plan_id',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let linkshow = ``;
                        if (abilityShow) {
                            linkshow =
                                `<a href="${urlShow.replace(':id',  row.id)}" class="action-btn btn-show" title="عرض"><i class="fas fa-eye"></i></a>`;
                        }
                        return `<div class="d-flex align-items-center justify-content-evenly">
                                    ${linkshow}
                                </div>`;
                    }
                }

            ];
        </script>

        <script type="text/javascript" src="{{ asset('js/custom/datatable.js') }}"></script>
    @endpush
</x-dashboard-layout>
