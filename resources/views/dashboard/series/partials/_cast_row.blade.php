@php
    $id_cast = $row['id'] ?? '';
@endphp
<div class="p-3 mb-3 rounded border shadow-sm cast-row card">
  <div class="row g-3 align-items-end">
            <input type="hidden" name="cast[{{ $i }}][id]" class="cast-id" value="{{ $id_cast }}">

    <div class="col-md-4">
      <label class="form-label small fw-bold">{{ __('admin.person') }}</label>
      <select class="form-select person-select"
              name="cast[{{ $i }}][person_id]"
              data-placeholder="{{ __('admin.person_placeholder') }}">
        @if(!empty($row['person_id']))
          <option value="{{ $row['person_id'] }}" selected>
            {{ $row['person_name'] ?? '—' }}
          </option>
        @endif
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label small fw-bold">{{ __('admin.role_type') }}</label>
      <select class="form-select" name="cast[{{ $i }}][role_type]" required>
        <option value="">{{ __('admin.role_type_placeholder') }}</option>
        @foreach($roleTypes as $key => $label)
          <option value="{{ $key }}"
            {{ (isset($row['role_type']) && $row['role_type'] === $key) ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label small fw-bold">{{ __('admin.character_name') }}</label>
      <input type="text" class="form-control" name="cast[{{ $i }}][character_name]"
             value="{{ $row['character_name'] ?? '' }}"
             placeholder="{{ __('admin.character_name_placeholder') }}">
    </div>

    <div class="col-md-2">
      <label class="form-label small fw-bold">{{ __('admin.ordering') }} </label>
      <div class="input-group">
        <input type="number" min="0" class="form-control" name="cast[{{ $i }}][sort_order]"
               value="{{ $row['sort_order'] ?? 0 }}"
               placeholder="{{ __('admin.ordering_placeholder') }}">
        <button type="button" class="btn btn-outline-danger remove-cast-row">
          {{ __('admin.delete_row') }}
        </button>
      </div>
    </div>
  </div>
</div>
