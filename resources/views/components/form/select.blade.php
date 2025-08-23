@props([
    'options' => [],
    'name',
    'id' => null,
    'label' => '',
    'value' => null,
    'required' => false,
])

@if ($label)
    <label class="mb-1 form-label fw-semibold text-dark d-flex align-items-center justify-content-between"
        for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="text-danger" style="font-size: 12px;">
                <i class="fa fa-asterisk"></i>
            </span>
        @endif
    </label>
@endif

<div class="custom-select-wrapper position-relative">
    <select id="{{ $id ?? $name }}" name="{{ $name }}" @required($required)
        {{ $attributes->class(['form-select custom-select-enhanced', 'is-invalid' => $errors->has($name)]) }}>
        <option value="" disabled @selected(old($name, $value) == null)>إختر القيمة</option>
        @foreach ($options as $item)
            <option value="{{ $item }}" @selected(old($name, $value) == $item)>{{ $item }}</option>
        @endforeach
    </select>

    {{-- يمكنك إضافة أيقونة السهم بشكل يدوي إن أردت تحسين شكله هنا --}}
</div>

{{-- Validation --}}
@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror
