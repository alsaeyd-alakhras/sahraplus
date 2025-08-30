<x-dashboard-layout>
    @push('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{asset('css/datatable/jquery.dataTables.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.bootstrap4.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.dataTables.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/buttons.dataTables.css')}}">

        {{-- sticky table --}}
        <link id="stickyTableLight" rel="stylesheet" href="{{ asset('css/custom/stickyTable.css') }}">

        {{-- custom css --}}
        <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex2.css') }}">
        <style>
            .btn-icon{ padding: 5px !important; }
            .btn-success { color:#fff !important; background-color:#28c76f !important; border-color:#28c76f !important; }
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

        @can('create', 'App\\Models\\Movie')
        <div class="mx-2 nav-item">
            <a href="{{ route('dashboard.movies.create') }}" class="m-0 btn btn-icon text-success">
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
        // عدلنا الحقول المعروضة: الحالة بدل is_active
        $fields = [
            'title_ar'   => 'العنوان بالعربي',
            'title_en'   => 'العنوان بالإنجليزي',
            'status'     => 'الحالة',
        ];
    @endphp

    <div class="shadow-lg enhanced-card">
        <div class="table-header-title">
            <i class="icon ph ph-movies me-2"></i>
            جدول الأفلام
        </div>
        <div class="enhanced-card-body">
            <div class="col-12" style="padding: 0;">
                <div class="table-container">
                    <table id="movies-table" class="table enhanced-sticky table-striped table-hover" style="display: table; width:100%; height: auto;">
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
                                                            data-bs-toggle="dropdown" id="btn-filter-{{ $loop->index + 1 }}">
                                                        <i class="fas fa-filter"></i>
                                                    </button>
                                                    <div class="dropdown-menu enhanced-filter-menu filterDropdownMenu" aria-labelledby="{{ $index }}_filter">
                                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox"
                                                                   placeholder="ابحث..." data-index="{{ $loop->index + 1 }}">
                                                            <button class="enhanced-apply-btn ms-2 filter-apply-btn-checkbox"
                                                                    data-target="{{ $loop->index + 1 }}" data-field="{{ $index }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="enhanced-checkbox-list checkbox-list-box">
                                                            <label style="display:block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="{{ $loop->index + 1 }}"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-{{ $loop->index + 1 }}"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="enhanced-sticky">العمليات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade delete-modal" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>تأكيد الحذف</h5>
            </div>
            <div class="modal-body">
                <div class="delete-icon"><i class="fas fa-trash-alt"></i></div>
                <div class="delete-warning-text">هل أنت متأكد؟</div>
                <p class="delete-sub-text">لن تتمكن من التراجع عن هذا الإجراء بعد الحذف!</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>إلغاء</button>
                <button type="button" class="text-white btn btn-confirm-delete" id="confirmDeleteBtn"><i class="fas fa-trash me-2"></i>حذف نهائي</button>
            </div>
        </div></div>
    </div>

    @push('scripts')
        <!-- DataTables JS -->
        <script src="{{asset('js/plugins/datatable/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.buttons.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.dataTables.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/jszip.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/pdfmake.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/vfs_fonts.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.html5.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.print.min.js')}}"></script>
        <script src="{{asset('js/plugins/jquery.validate.min.js')}}"></script>

        <script>
            const tableId = 'movies-table';
            const arabicFileJson = "{{ asset('files/Arabic.json')}}";
            const pageLength = $('#advanced-pagination').val();

            // urls
            const _token     = "{{ csrf_token() }}";
            const urlIndex   = `{{ route('dashboard.movies.index') }}`;
            const urlFilters = `{{ route('dashboard.movies.filters', ':column') }}`; // ← صححنا اسم الراوت
            const urlCreate  = '{{ route("dashboard.movies.create") }}';
            const urlShow    = '{{ route("dashboard.movies.show", ":id") }}';
            const urlEdit    = '{{ route("dashboard.movies.edit", ":id") }}';
            const urlDelete  = '{{ route("dashboard.movies.destroy", ":id") }}';

            // ability
            const abilityCreate = "{{ Auth::guard('admin')->user()->can('create', 'App\\Models\\Movie') }}";
            const abilityShow   = "{{ Auth::guard('admin')->user()->can('show',   'App\\Models\\Movie') }}";
            const abilityEdit   = "{{ Auth::guard('admin')->user()->can('update', 'App\\Models\\Movie') }}";
            const abilityDelete = "{{ Auth::guard('admin')->user()->can('delete', 'App\\Models\\Movie') }}";

            // أسماء الحقول للفلترة في الهيدر
            const fields = [
                '#',
                'title_ar',
                'title_en',
                'status', // ← بدل is_active
            ];

            // أعمدة الداتا تيبل: نستخدم status_label الراجعة من السيرفس
            const columnsTable = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, class: 'text-center' },

                { data: 'title_ar', name: 'title_ar', orderable: false, render: function (data) {
                    return data ?? '';
                }},

                { data: 'title_en', name: 'title_en', orderable: false, render: function (data) {
                    return data ?? '';
                }},

                // الحالة (badge)
                { data: 'status_label', name: 'status', orderable: false, render: function (label, type, row) {
                    // label قيمتها: مسودة/منشور/مؤرشف - صادرة من السيرفس
                    const map = {
                        'منشور':  'bg-success',
                        'مؤرشف':  'bg-secondary',
                        'مسودة':  'bg-warning'
                    };
                    const cls = map[label] ?? 'bg-light text-dark';
                    return `<span class="badge ${cls}">${label ?? ''}</span>`;
                }},

                // العمليات
                { data: 'edit', name: 'edit', orderable: false, searchable: false, render: function (id) {
                    let linkshow = ``;
                    let linkedit = ``;
                    let linkdelete = ``;

                    if (abilityShow) {
                        linkshow = `<a href="${urlShow.replace(':id', id)}" class="action-btn btn-show" title="عرض"><i class="fas fa-eye"></i></a>`;
                    }
                    if (abilityEdit) {
                        linkedit = `<a href="${urlEdit.replace(':id', id)}" class="action-btn btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>`;
                    }
                    if (abilityDelete) {
                        linkdelete = `<button class="action-btn btn-delete delete_row" data-id="${id}" title="حذف"><i class="fas fa-trash"></i></button>`;
                    }

                    return `<div class="d-flex align-items-center justify-content-evenly">
                                ${linkshow}${linkedit}${linkdelete}
                            </div>`;
                }},
            ];
        </script>
        <script type="text/javascript" src="{{asset('js/custom/datatable.js')}}"></script>
    @endpush
</x-dashboard-layout>
