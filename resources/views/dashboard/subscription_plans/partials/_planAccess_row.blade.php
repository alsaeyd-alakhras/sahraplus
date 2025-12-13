@php
    $id_planContentAccess = $row['id'] ?? '';
    $content_type = $row['content_type'] ?? null;
    $content_id = $row['content_id'] ?? '';
    $access_type = $row['access_type'] ?? '';
@endphp
<div class="p-3 mb-3 rounded border shadow-sm planAccess-r-row card">

    <div class="row g-3 align-items-end">
        <input type="hidden" name="planAccess[{{ $i }}][id]" class="planAccess-id" value="{{ $id_planContentAccess }}">
        
        <div class="col-md-3">
            <x-form.selectkey label="{{ __('admin.content_type') }}" class="content_type_select" name="planAccess[{{ $i }}][content_type]"
                :selected="$content_type ?? null" :options="$contentAccessOptions"  data-index="{{ $i }}" />
        </div>

        <div class="col-md-3">
            <label for="content_id" class="form-label fw-bold">{{ __('admin.content_id') }}</label>
            <select name="planAccess[{{ $i }}][content_id]" id="content_id" class="form-select">
                <option value="">{{ __('admin.select_content') }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <x-form.selectkey label="{{ __('admin.access_type') }}" name="planAccess[{{ $i }}][access_type]"
                :selected="$access_type ?? 'allow'" :options="$accessTypeOptions" />
        </div>

        <!-- الترتيب + حذف -->
        <div class="col-md-2">
            <div class="input-group">
                <button type="button" class="btn btn-outline-danger remove-planAccess-row">
                    {{ __('admin.delete_row') }}
                </button>
            </div>
        </div>

    </div>
</div>



@push('scripts')
<script>
    $(document).ready(function() {

        function loadContents(type, index, selectedId = null) {
            let contentSelect = $('select[name="planAccess[' + index + '][content_id]"]');
            contentSelect.empty().append('<option value="">{{ __('admin.loading') }}...</option>');
            if (type) {
                $.ajax({
                    url: '{{ route('dashboard.plan_access.getContents') }}',
                    type: 'GET',
                    data: {
                        type: type
                    },
                    success: function(data) {
                        contentSelect.empty().append(
                            '<option value="">{{ __('admin.select_content') }}</option>');
                        $.each(data, function(key, value) {
                            let selected = selectedId && selectedId == value.id ?
                                'selected' : '';
                            contentSelect.append('<option value="' + value.id + '" ' +
                                selected + '>' + value.name + '</option>');
                        });
                    },
                    error: function() {
                        contentSelect.empty().append(
                            '<option value="">{{ __('admin.error_loading') }}</option>');
                    }
                });
            } else {
                contentSelect.empty().append('<option value="">{{ __('admin.select_content') }}</option>');
            }
        }

        // عند تغيير النوع
        // $('.content_type_select').on('change', function() {
        //     let type = $(this).val();
        //     let index = $(this).data('index');
        //     loadContents(type, index);
        // });
        $(document).on('change', '.content_type_select', function() {
            let type = $(this).val();
            let index = $(this).data('index');
            loadContents(type, index);
        });

        // let index = $('.content_type_select').data('index');
        // // عند التحميل: إذا كان هناك نوع محتوى موجود مسبقًا (للتعديل)، نحمل العناصر مع تحديد العنصر الحالي
        // let initialType = $('.content_type_select').val();
        // let initialContentId = $('select[name="planAccess[' + index + '][content_id]"]').val();
        // if (initialType) {
        //     loadContents(initialType, index, initialContentId);
        // }
    });
</script>
@endpush