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
            /* ضمان تمدد الجدول كامل العرض */
            .table-container{ width:100%; overflow-x:auto; }
            #shorts-table, #shorts-table.dataTable { width:100% !important; }
            .dataTables_wrapper .dataTables_scrollHead,
            .dataTables_wrapper .dataTables_scrollHeadInner { width:100% !important; }
            .btn-icon{ padding:5px !important; }
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

        {{-- زر إنشاء --}}
        @can('create', 'App\\Models\\Short')
        <div class="mx-2 nav-item">
            <a href="{{ route('dashboard.shorts.create') }}" class="m-0 btn btn-icon text-success" title="إضافة">
                <i class="fa-solid fa-plus fe-16"></i>
            </a>
        </div>
        @endcan

        {{-- إزالة التصفية --}}
        <div class="mx-2 nav-item">
            <button class="p-2 border-0 btn btn-outline-danger rounded-pill me-n1 waves-effect waves-light d-none"
                    type="button" id="filterBtnClear" title="إزالة التصفية">
                <i class="fa-solid fa-eraser fe-16"></i>
            </button>
        </div>

        {{-- تحديث --}}
        <div class="mx-2 nav-item d-flex align-items-center justify-content-center">
            <button type="button" class="btn" id="refreshData" title="تحديث">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </x-slot:extra_nav>

    @php
        // الأعمدة الظاهرة (العنوان – الحالة – مميز)
        $fields = [
            'title'       => 'العنوان',
            'status'      => 'الحالة',
            'is_featured' => 'مميز؟',
        ];
    @endphp

    <div class="shadow-lg enhanced-card">
        <div class="table-header-title">
            <i class="icon ph ph-video me-2"></i>
            جدول الفيديوهات القصيرة
        </div>
        <div class="enhanced-card-body">
            <div class="col-12" style="padding:0;">
                <div class="table-container">
                    <table id="shorts-table" class="table enhanced-sticky table-striped table-hover" style="width:100%;">
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
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="{{ $loop->index + 1 }}">
                                                                الكل
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
            const tableId        = 'shorts-table';
            const arabicFileJson = "{{ asset('files/Arabic.json')}}";
            const pageLength     = $('#advanced-pagination').val();
            const _token         = "{{ csrf_token() }}";

            // routes
            const urlIndex   = `{{ route('dashboard.shorts.index') }}`;
            const urlFilters = `{{ route('dashboard.short.filters', ':column') }}`;
            const urlCreate  = '{{ route("dashboard.shorts.create") }}';
            const urlShow    = '{{ route("dashboard.shorts.show", ":id") }}';
            const urlEdit    = '{{ route("dashboard.shorts.edit", ":id") }}';
            const urlDelete  = '{{ route("dashboard.shorts.destroy", ":id") }}';

            // abilities
            const abilityCreate = "{{ Auth::guard('admin')->user()->can('create', 'App\\Models\\Short') }}";
            const abilityShow   = "{{ Auth::guard('admin')->user()->can('show',   'App\\Models\\Short') }}";
            const abilityEdit   = "{{ Auth::guard('admin')->user()->can('update', 'App\\Models\\Short') }}";
            const abilityDelete = "{{ Auth::guard('admin')->user()->can('delete', 'App\\Models\\Short') }}";

            // fields for header filters
            const fields = ['#','title','status','is_featured'];

            // DataTable columns (تطابق مخرجات ShortService@datatableIndex)
            const columnsTable = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, class:'text-center' },

                { data: 'title', name: 'title', orderable:false, render: function(d){ return d ?? ''; } },

                // الحالة: السيرفس يرجّع 'status' بالعربي (نشط/غير نشط)
                { data: 'status', name: 'status', orderable:false, render: function(label){
                    const cls = (label === 'نشط') ? 'bg-success' : 'bg-secondary';
                    return `<span class="badge ${cls}">${label ?? ''}</span>`;
                }},

                // مميز؟ السيرفس يرجّع is_featured كنص (مميز/عادي)
                { data: 'is_featured', name: 'is_featured', orderable:false, render: function(label){
                    const cls = (label === 'مميز') ? 'bg-info' : 'bg-light text-dark';
                    return `<span class="badge ${cls}">${label ?? ''}</span>`;
                }},

                // العمليات
                { data: 'edit', name: 'edit', orderable:false, searchable:false, render: function(id){
                    let linkshow = '', linkedit = '', linkdelete = '';
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

            // إصلاح تمدد الأعمدة بعد التحميل
            $(document).on('init.dt', function (e, settings) {
                if (settings.nTable && settings.nTable.id === tableId) {
                    const api = new $.fn.dataTable.Api(settings);
                    api.columns.adjust().draw(false);
                }
            });
        </script>

        <script type="text/javascript" src="{{asset('js/custom/datatable.js')}}"></script>
    @endpush
</x-dashboard-layout>
