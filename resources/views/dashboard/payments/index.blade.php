<x-dashboard-layout>
    @push('styles')
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
        {{-- excel export --}}
        <div class="mx-2 nav-item">
            <button type="button" class="text-white btn btn-icon btn-success" id="excel-export" title="تصدير excel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="16" height="16">
                    <path
                        d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM155.7 250.2L192 302.1l36.3-51.9c7.6-10.9 22.6-13.5 33.4-5.9s13.5 22.6 5.9 33.4L221.3 344l46.4 66.2c7.6 10.9 5 25.8-5.9 33.4s-25.8 5-33.4-5.9L192 385.8l-36.3 51.9c-7.6 10.9-22.6 13.5-33.4 5.9s-13.5-22.6-5.9-33.4L162.7 344l-46.4-66.2c-7.6-10.9-5-25.8 5.9-33.4s25.8-5 33.4 5.9z" />
                </svg>
            </button>
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
            'subscription_id' => __('admin.sub_plans'),
            'payment_reference' => __('admin.payment_reference'),
            'amount' => __('admin.amount'),
            'currency' => __('admin.currency'),
            'status' => __('admin.status'),
        ];
    @endphp
    {{-- تم تغيير معرف الجدول إلى payments-table ليكون أكثر دلالة --}}
    <div class="shadow-lg enhanced-card">
        <div class="table-header-title">
            <i class="icon ph ph-money me-2"></i> {{-- تم تغيير الأيقونة لتناسب المدفوعات --}}
            {{ __('admin.payments') }}
        </div>
        <div class="enhanced-card-body">
            <div class="col-12" style="padding: 0;">
                <div class="table-container">
                    {{-- تم تغيير id الجدول --}}
                    <table id="payments-table"
                        class="table enhanced-sticky table-striped table-hover"style="display: table; width:100%; height: auto;">
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
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all"
                                                                    class="all-checkbox"
                                                                    data-index="{{ $loop->index + 1 }}">
                                                                {{ __('admin.All') }}
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

    <div class="modal fade delete-modal" id="deleteConfirmModal" tabindex="-1"
        aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('admin.Delete Confirmation') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="delete-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="delete-warning-text">{{ __('admin.Are you sure?') }}</div>
                    <p class="delete-sub-text">
                        {{ __('admin.You will not be able to revert this!') }}
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        {{ __('admin.Cancel') }}
                    </button>
                    <button type="button" class="text-white btn btn-confirm-delete" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i>
                        {{ __('admin.Final_Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
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

        {{-- script --}}
        <script>
            // 1. **تعديل معرف الجدول (ID) ليطابق HTML:**
            const tableId = 'payments-table'; // تم تعديل من 'countries-table'

            const arabicFileJson = "{{ asset('files/Arabic.json') }}";

            const pageLength = $('#advanced-pagination').val();

            // urls
            const _token = "{{ csrf_token() }}";
            const urlIndex = `{{ route('dashboard.payments.index') }}`;
            const urlFilters = `{{ route('dashboard.payments.filters', ':column') }}`;
            const urlShow = '{{ route('dashboard.payments.show', ':id') }}';
            // (لنفترض أن لديك زر حذف)
            const urlDelete = '{{ route('dashboard.payments.destroy', ':id') }}';

            // 2. **تعديل صلاحيات العرض والحذف لتناسب موديل Payment:**
            const abilityShow = "{{ Auth::guard('admin')->user()->can('show', 'App\\Models\\Payments') }}";
            const abilityDelete =
                "{{ Auth::guard('admin')->user()->can('delete', 'App\\Models\\Payments') }}";

            const fields = [
                '#',
                'user_id',
                'subscription_id',
                'payment_reference',
                'amount',
                'currency',
                'status'
            ];
            const columnsTable = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    class: 'text-center'
                }, // عمود الترقيم التلقائي
                {
                    data: 'user_id',
                    name: 'user_id',
                    orderable: false,
                    render: function(data, type, row) {
                        return data ?? '';
                    }
                },
                {
                    data: 'subscription_id',
                    name: 'subscription_id',
                    orderable: false,
                    render: function(data, type, row) {
                        return data ?? '';
                    }
                }, {
                    data: 'payment_reference',
                    name: 'payment_reference',
                    orderable: false,
                    render: function(data, type, row) {
                        return data ?? '';
                    }
                }, {
                    data: 'amount',
                    name: 'amount',
                    orderable: false,
                    render: function(data, type, row) {
                        return data ?? '';
                    }
                }, {
                    data: 'currency',
                    name: 'currency',
                    orderable: false,
                    render: function(data, type, row) {
                        return data ?? '';
                    }
                } ,
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    render: function(data, type, row) {

                        let color = 'bg-secondary';

                        switch (data) {
                            case 'قيد الانتظار':
                            case 'pending': // إضافة حالة إنجليزية محتملة
                                color = 'bg-warning';
                                break;
                            case 'قيد المعالجة':
                            case 'processing': // إضافة حالة إنجليزية محتملة
                                color = 'bg-info';
                                break;
                            case 'مكتمل':
                            case 'completed': // إضافة حالة إنجليزية محتملة
                                color = 'bg-success';
                                break;
                            case 'فشل':
                            case 'failed': // إضافة حالة إنجليزية محتملة
                                color = 'bg-danger';
                                break;
                            case 'مسترجع':
                            case 'refunded': // إضافة حالة إنجليزية محتملة
                                color = 'bg-primary';
                                break;
                            case 'ملغي':
                            case 'cancelled': // إضافة حالة إنجليزية محتملة
                                color = 'bg-dark';
                                break;
                        }

                        // للتأكد من عرض الحالة المناسبة سواء عربية أو إنجليزية
                        let statusText = data;

                        return `<span class="badge ${color}">${statusText}</span>`;
                    }
                },

                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let linkshow = ``;
                        let linkdelete = ``; // 3. **تعريف متغير زر الحذف**

                        if (abilityShow) {
                            linkshow =
                                `<a href="${urlShow.replace(':id',  row.id)}" class="action-btn btn-show" title="عرض"><i class="fas fa-eye"></i></a>`;
                        }

                        if (abilityDelete) {
                            linkdelete = `
                            <button class="action-btn btn-delete delete_row"
                                    data-id="${row.id}" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>`;
                        }

                        // 6. **إرجاع جميع الأزرار المتاحة**
                        return `<div class="d-flex align-items-center justify-content-evenly">
                                    ${linkshow}
                                    ${linkdelete}
                                </div>`;
                    }
                },
            ];
            // ملاحظة: يُرجى التأكد من أن ملف datatable.js يستخدم المتغيرات المحدثة مثل tableId و urlDestroy و abilityDelete.
        </script>
        <script type="text/javascript" src="{{ asset('js/custom/datatable.js') }}"></script>
    @endpush
</x-dashboard-layout>
