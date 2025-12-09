@php
    $id_cast = $row['id'] ?? '';
@endphp
<div class="p-3 mb-3 rounded border shadow-sm cast-row card">

    <div class="row g-3 align-items-end">
        <input type="hidden" name="cast[{{ $i }}][id]" class="cast-id" value="{{ $id_cast }}">

        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.limitation_type') }}</label>
            <input type="text" class="form-control" name="cast[{{ $i }}][limitation_type]"
                value="{{ $row['limitation_type'] ?? '' }}" placeholder="{{ __('admin.limitation_type') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.limitation_key') }}</label>
            <input type="text" class="form-control" name="cast[{{ $i }}][limitation_key]"
                value="{{ $row['limitation_key'] ?? '' }}" placeholder="{{ __('admin.limitation_key') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.limitation_value') }}</label>
            <input type="text" class="form-control" name="cast[{{ $i }}][limitation_value]"
                value="{{ $row['limitation_value'] ?? '' }}" placeholder="{{ __('admin.limitation_value') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.limitation_unit') }}</label>
            <input type="text" class="form-control" name="cast[{{ $i }}][limitation_unit]"
                value="{{ $row['limitation_unit'] ?? '' }}" placeholder="{{ __('admin.limitation_unit') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.description_ar') }}</label>
            <input type="text" class="form-control" name="cast[{{ $i }}][description_ar]"
                value="{{ $row['description_ar'] ?? '' }}" placeholder="{{ __('admin.description_ar') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.description_en') }}</label>
            <input type="text" class="form-control" name="cast[{{ $i }}][description_en]"
                value="{{ $row['description_en'] ?? '' }}" placeholder="{{ __('admin.description_en') }}">
        </div>

        <!-- الترتيب + حذف -->
        <div class="col-md-2">
            <div class="input-group">
                <button type="button" class="btn btn-outline-danger remove-cast-sub-row">
                    {{ __('admin.delete_row') }}
                </button>
            </div>
        </div>

    </div>
</div>
