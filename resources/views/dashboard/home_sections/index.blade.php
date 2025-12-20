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

        @can('create', 'App\\Models\\HomeSection')
            <div class="mx-2 nav-item">
                <a href="{{ route('dashboard.home_sections.create') }}" class="m-0 btn btn-icon text-success">
                    <i class="fa-solid fa-plus fe-16"></i>
                </a>
            </div>
        @endcan

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
            'title_ar' => __('admin.title_ar'),
            'title_en' => __('admin.title_en'),
            'platform' => __('admin.platform'),
            'is_kids' => __('admin.is_kids'),
            'sort_order' => __('admin.Sort_order'),
            'is_active' => __('admin.Is_active'),
        ];
    @endphp

    <div class="shadow-lg enhanced-card">
        <div class="table-header-title">
            <i class="icon ph  ph-list me-2"></i>
            {{ __('admin.home_sections') }}
        </div>
        <div class="enhanced-card-body">
            <div class="col-12" style="padding: 0;">
                <div class="table-container">
                    <table id="home-sections-table" class="table enhanced-sticky table-striped table-hover"
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
                                <th class="enhanced-sticky">{{ __('admin.Action') }}</th>
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
                            class="fas fa-times me-2"></i>{{ __('admin.Cancel') }}</button>
                    <button type="button" class="text-white btn btn-confirm-delete" id="confirmDeleteBtn"><i
                            class="fas fa-trash me-2"></i>{{ __('admin.Final_Delete') }}</button>
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
            const tableId = 'home-sections-table';
            const arabicFileJson = "{{ asset('files/Arabic.json') }}";
            const pageLength = $('#advanced-pagination').val();

            // urls
            const _token = "{{ csrf_token() }}";
            const urlIndex = `{{ route('dashboard.home_sections.index') }}`;
            const urlDelete = '{{ route('dashboard.home_sections.destroy', ':id') }}';

            const urlFilters = `{{ route('dashboard.home_sections.filters', ':column') }}`;

            const urlCreate = '{{ route('dashboard.home_sections.create') }}';
            const urlEdit = '{{ route('dashboard.home_sections.edit', ':id') }}';

            // ability
            const abilityCreate = "{{ Auth::guard('admin')->user()->can('create', 'App\\Models\\HomeSection') }}";
            const abilityShow = "{{ Auth::guard('admin')->user()->can('show', 'App\\Models\\HomeSection') }}";
            const abilityEdit = "{{ Auth::guard('admin')->user()->can('update', 'App\\Models\\HomeSection') }}";
            const abilityDelete = "{{ Auth::guard('admin')->user()->can('delete', 'App\\Models\\HomeSection') }}";

            // أسماء الحقول للفلترة في الهيدر
            const fields = ['#', 'title_ar', 'title_en', 'platform', 'is_kids', 'sort_order', 'is_active'];

            // أعمدة الداتا تيبل
            const columnsTable = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    class: 'text-center'
                },
                {
                    data: 'title_ar',
                    name: 'title_ar',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'title_en',
                    name: 'title_en',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'platform',
                    name: 'platform',
                    orderable: false,
                    render: (d) => d ?? ''
                },
                {
                    data: 'is_kids',
                    name: 'is_kids',
                    orderable: false,
                    render: function(d) {
                        const isKids = (d === '{{ __('admin.Yes') }}' || d === true || d === 1);
                        return `<span class="badge ${isKids?'bg-info':'bg-secondary'}">${isKids?'{{ __('admin.Yes') }}':'{{ __('admin.No') }}'}</span>`;
                    }
                },
                {
                    data: 'sort_order',
                    name: 'sort_order',
                    orderable: false,
                    class: 'text-center'
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: false,
                    render: function(d) {
                        const active = (d === '{{ __('admin.Active') }}' || d === true || d === 1);
                        return `<span class="badge ${active?'bg-success':'bg-secondary'}">${active?'{{ __('admin.Active') }}':'{{ __('admin.Inactive') }}'}</span>`;
                    }
                },
                // العمليات
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let linkedit = ``;
                        let linkdelete = ``;
                        if (abilityEdit) {
                            linkedit =
                                `<a href="${urlEdit.replace(':id', data)}" class="action-btn btn-edit" title="{{ __('admin.Edit') }}"><i class="fas fa-edit"></i></a>`;
                        }
                        if (abilityDelete) {
                            linkdelete =
                                `<button class="action-btn btn-delete delete_row" data-id="${data}" title="{{ __('admin.Delete') }}"><i class="fas fa-trash"></i></button>`;
                        }

                        return `<div class="d-flex align-items-center justify-content-evenly">
                                ${linkedit}${linkdelete}
                            </div>`;
                    }
                },
            ];
        </script>
        <script type="text/javascript" src="{{ asset('js/custom/datatable.js') }}"></script>
    @endpush
</x-dashboard-layout>

