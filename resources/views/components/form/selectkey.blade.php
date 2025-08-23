@props([
    'options' => [],
    'name',
    'id' => null,
    'label' => '',
    'selected' => null,
    'required' => false,
])

@if ($label)
    <label class="mb-1 form-label fw-semibold text-dark d-flex align-items-center justify-content-between"
        for="{{ $id ?? $name }}">
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

        <option value="" disabled @selected(old($name, $selected) === null)>إختر القيمة</option>

        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected(old($name, $selected) == $key)>
                {{ $text }}
            </option>
        @endforeach
    </select>

    {{-- ملاحظة: يمكنك هنا إضافة SVG أو أيقونة FontAwesome للسهم داخل select --}}
</div>

@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror
