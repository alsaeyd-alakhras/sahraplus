@php
    $id_sectionItem = $row['id'] ?? '';
    $content_type = $row['content_type'] ?? null;
    $content_id = $row['content_id'] ?? '';
    $sort_order = $row['sort_order'] ?? $i;
@endphp
<div class="p-3 mb-3 rounded border shadow-sm sectionItem-r-row card" data-index="{{ $i }}">

    <div class="row g-3 align-items-end">
        <input type="hidden" name="sectionItems[{{ $i }}][id]" class="sectionItem-id" value="{{ $id_sectionItem }}">
        
        <div class="col-md-3">
            <x-form.selectkey label="{{ __('admin.content_type') }}" class="content_type_select" name="sectionItems[{{ $i }}][content_type]"
                :selected="$content_type ?? null" :options="$contentTypeOptions"  data-index="{{ $i }}" />
        </div>

        <div class="col-md-3">
            <label for="content_id" class="form-label fw-bold">{{ __('admin.content_id') }}</label>
            <select name="sectionItems[{{ $i }}][content_id]" id="content_id" class="form-select" data-value="{{ $content_id }}">
                <option value="">{{ __('admin.select_content') }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <x-form.input type="number" label="{{ __('admin.Sort_order') }}" name="sectionItems[{{ $i }}][sort_order]"
                :value="$sort_order ?? $i" min="0" />
        </div>

        <!-- حذف -->
        <div class="col-md-2">
            <div class="input-group">
                <button type="button" class="btn btn-outline-danger remove-sectionItem-row">
                    {{ __('admin.delete_row') }}
                </button>
            </div>
        </div>

    </div>
</div>

