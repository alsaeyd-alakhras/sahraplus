<x-dashboard-layout>
    @push('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{asset('css/datatable/jquery.dataTables.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.bootstrap4.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.dataTables.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/buttons.dataTables.css')}}">
        <link id="stickyTableLight" rel="stylesheet" href="{{ asset('css/custom/stickyTable.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex2.css') }}">
    @endpush

    <x-slot:extra_nav>
        <div class="nav-item">
            <select class="form-control" id="advanced-pagination">
                <option value="10">10</option><option value="25">25</option>
                <option value="100">100</option><option value="500">500</option>
                <option value="-1">all</option>
            </select>
        </div>

        @can('create','App\\Models\\MovieCategory')
        <div class="mx-2 nav-item">
            <a href="{{ route('dashboard.movie-categories.create') }}" class="m-0 btn btn-icon text-success">
                <i class="fa-solid fa-plus fe-16"></i>
            </a>
        </div>
        @endcan
    </x-slot:extra_nav>

    @php
        $fields = [
            'name_ar'   => 'الاسم (عربي)',
            'name_en'   => 'الاسم (EN)',
            'is_active' => 'الحالة',
        ];
    @endphp

    <div class="shadow-lg enhanced-card">
        <div class="table-header-title">
            <i class="icon ph ph-cards me-2"></i>
            جدول التصنيفات
        </div>
        <div class="enhanced-card-body">
            <div class="col-12" style="padding:0;">
                <div class="table-container">
                    <table id="movie-categories-table" class="table enhanced-sticky table-striped table-hover" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                @foreach($fields as $index => $label)
                                    <th>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>{{ $label }}</span>
                                            <div class="enhanced-filter-dropdown">
                                                <div class="dropdown">
                                                    <button class="enhanced-btn-filter btn-filter" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-filter"></i>
                                                    </button>
                                                    <div class="dropdown-menu enhanced-filter-menu filterDropdownMenu">
                                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" placeholder="ابحث..." data-index="{{ $loop->index+1 }}">
                                                            <button class="enhanced-apply-btn ms-2 filter-apply-btn-checkbox" data-target="{{ $loop->index+1 }}" data-field="{{ $index }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="enhanced-checkbox-list checkbox-list-box">
                                                            <label style="display:block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="{{ $loop->index+1 }}"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-{{ $loop->index+1 }}"></div>
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
        <script src="{{asset('js/plugins/datatable/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.buttons.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.dataTables.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/jszip.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/pdfmake.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/vfs_fonts.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.html5.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.print.min.js')}}"></script>

        <script>
            const tableId     = 'movie-categories-table';
            const arabicFileJson = "{{ asset('files/Arabic.json')}}";
            const pageLength  = $('#advanced-pagination').val();
            const _token      = "{{ csrf_token() }}";

            const urlIndex   = `{{ route('dashboard.movie-categories.index') }}`;
            const urlFilters = `{{ route('dashboard.movie-categories.filters', ':column') }}`;
            const urlCreate  = '{{ route("dashboard.movie-categories.create") }}';
            const urlShow    = '{{ route("dashboard.movie-categories.show", ":id") }}';
            const urlEdit    = '{{ route("dashboard.movie-categories.edit", ":id") }}';
            const urlDelete  = '{{ route("dashboard.movie-categories.destroy", ":id") }}';

            const abilityShow   = "{{ Auth::guard('admin')->user()->can('show','App\\Models\\MovieCategory') }}";
            const abilityEdit   = "{{ Auth::guard('admin')->user()->can('update','App\\Models\\MovieCategory') }}";
            const abilityDelete = "{{ Auth::guard('admin')->user()->can('delete','App\\Models\\MovieCategory') }}";

            const fields = ['#','name_ar','name_en','is_active'];

            const columnsTable = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, class:'text-center' },
                { data: 'name_ar', name: 'name_ar', orderable:false, render: (d)=> d ?? '' },
                { data: 'name_en', name: 'name_en', orderable:false, render: (d)=> d ?? '' },
                { data: 'is_active', name: 'is_active', orderable:false, render: function (d) {
                    const active = (d === 'نشط' || d === true || d === 1);
                    return `<span class="badge ${active?'bg-success':'bg-secondary'}">${active?'نشط':'غير نشط'}</span>`;
                }},
                { data: 'edit', name: 'edit', orderable:false, searchable:false, render: function (id) {
                    let linkshow='', linkedit='', linkdelete='';
                    if (abilityShow)   linkshow   = `<a href="${urlShow.replace(':id',id)}" class="action-btn btn-show"><i class="fas fa-eye"></i></a>`;
                    if (abilityEdit)   linkedit   = `<a href="${urlEdit.replace(':id',id)}" class="action-btn btn-edit"><i class="fas fa-edit"></i></a>`;
                    if (abilityDelete) linkdelete = `<button class="action-btn btn-delete delete_row" data-id="${id}"><i class="fas fa-trash"></i></button>`;
                    return `<div class="d-flex align-items-center justify-content-evenly">${linkshow}${linkedit}${linkdelete}</div>`;
                }},
            ];
        </script>
        <script type="text/javascript" src="{{asset('js/custom/datatable.js')}}"></script>
    @endpush
</x-dashboard-layout>
