<x-dashboard-layout>
    @push('styles')
        <link rel="stylesheet" href="{{asset('css/datatable/jquery.dataTables.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.bootstrap4.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.dataTables.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/buttons.dataTables.css')}}">
        <link id="stickyTableLight" rel="stylesheet" href="{{ asset('css/custom/stickyTable.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex2.css') }}">

        {{-- ✅ اجبار الجدول ياخذ كامل العرض --}}
        <style>
            .table-container{ width:100%; overflow-x:auto; }
            #people-table,
            #people-table.dataTable { width:100% !important; }
            .dataTables_wrapper .dataTables_scrollHead,
            .dataTables_wrapper .dataTables_scrollHeadInner { width:100% !important; }
        </style>
    @endpush>

    <x-slot:extra_nav>
        <div class="nav-item">
            <select class="form-control" id="advanced-pagination">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="100">100</option>
                <option value="500">500</option>
                <option value="-1">all</option>
            </select>
        </div>
        @can('create', 'App\\Models\\Person')
        <div class="mx-2 nav-item">
            <a href="{{ route('dashboard.people.create') }}" class="m-0 btn btn-icon text-success">
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
            'name_ar'  => __('admin.Name_ar'),
            'name_en'  => __('admin.Name_en'),
            'birth_date' => __('site.birth_date'),
            'birth_place' => __('admin.birth_place'),
            'known_for' => __('admin.known_for'),
            'is_active'=> __('admin.is_active'),
        ];
    @endphp

    <div class="shadow-lg enhanced-card">
        <div class="table-header-title">
            <i class="icon ph ph-user me-2"></i>
            {{ __('admin.People') }}
        </div>
        <div class="enhanced-card-body">
            <div class="col-12" style="padding:0;">
                <div class="table-container">
                    <table id="people-table" class="table enhanced-sticky table-striped table-hover" style="display: table; width:100%; height: auto;">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                @foreach ($fields as $index => $label)
                                    <th>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>{{ $label }}</span>
                                            <div class="enhanced-filter-dropdown">
                                                <div class="dropdown">
                                                    <button class="enhanced-btn-filter btn-filter" type="button" data-bs-toggle="dropdown" id="btn-filter-{{ $loop->index + 1 }}">
                                                        <i class="fas fa-filter"></i>
                                                    </button>
                                                    <div class="dropdown-menu enhanced-filter-menu filterDropdownMenu" aria-labelledby="{{ $index }}_filter">
                                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" placeholder="ابحث..." data-index="{{ $loop->index + 1 }}">
                                                            <button class="enhanced-apply-btn ms-2 filter-apply-btn-checkbox" data-target="{{ $loop->index + 1 }}" data-field="{{ $index }}">
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
                                <th>العمليات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('js/plugins/datatable/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.buttons.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.dataTables.js')}}"></script>

        <script>
            const tableId     = 'people-table';
            const arabicFileJson = "{{ asset('files/Arabic.json')}}";
            const pageLength  = $('#advanced-pagination').val();
            const _token      = "{{ csrf_token() }}";

            const urlIndex   = `{{ route('dashboard.people.index') }}`;
            const urlFilters = `{{ route('dashboard.people.filters', ':column') }}`; // ✅ اسم الراوت الصحيح
            const urlCreate  = '{{ route("dashboard.people.create") }}';
            const urlEdit    = '{{ route("dashboard.people.edit", ":id") }}';
            const urlDelete  = '{{ route("dashboard.people.destroy", ":id") }}';

            const abilityCreate = "{{ Auth::guard('admin')->user()->can('create', 'App\\Models\\Person') }}";
            const abilityShow   = "{{ Auth::guard('admin')->user()->can('show',   'App\\Models\\Person') }}";
            const abilityEdit   = "{{ Auth::guard('admin')->user()->can('update', 'App\\Models\\Person') }}";
            const abilityDelete = "{{ Auth::guard('admin')->user()->can('delete', 'App\\Models\\Person') }}";

            const fields = ['#','name_ar','name_en','birth_date','birth_place','known_for','is_active'];

            const columnsTable = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, class:'text-center' },
                { data: 'name_ar', name: 'name_ar', orderable:false},
                { data: 'name_en', name: 'name_en', orderable:false},
                { data: 'birth_date', name: 'birth_date', orderable:false},
                { data: 'birth_place', name: 'birth_place', orderable:false},
                { data: 'known_for', name: 'known_for', orderable:false},
                { data: 'is_active', name: 'is_active', orderable:false, render: function (data) {
                    const active = (data === 'نشط');
                    return `<span class="badge ${active ? 'bg-success' : 'bg-secondary'}">${active ? 'نشط' : 'غير نشط'}</span>`;
                }},
                { data: 'edit', name: 'edit', orderable:false, searchable:false, render: function (id) {
                    let linkedit = '', linkdelete = '';
                    if (abilityEdit)  linkedit  = `<a href="${urlEdit.replace(':id', id)}" class="action-btn btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>`;
                    if (abilityDelete)linkdelete= `<button class="action-btn btn-delete delete_row" data-id="${id}" title="حذف"><i class="fas fa-trash"></i></button>`;
                    return `<div class="d-flex align-items-center justify-content-evenly">${linkedit}${linkdelete}</div>`;
                }},
            ];
        </script>

        <script type="text/javascript" src="{{asset('js/custom/datatable.js')}}"></script>
    @endpush
</x-dashboard-layout>
