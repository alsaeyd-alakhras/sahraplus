@props([
    'type' => 'text',
    'value' => '',
    'name',
    'id' => null,
    'label'=>'',
    'required' => false,
])
@if ($label)
    <label class="mb-1 form-label fw-semibold text-dark d-flex align-items-center justify-content-between" for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="text-danger" style="font-size: 12px;">
                <i class="fa fa-asterisk"></i>
            </span>
        @endif
    </label>
@endif

<input
    type="{{$type}}"
    id="{{$id ?? $name}}"
    name="{{$name}}"
    value="{{old($name, $value)}}"
    autocomplete="off"
    {{$attributes->class([
        'form-control',
        'is-invalid' => $errors->has($name)
    ])}}
/>

{{-- Validation --}}
@error($name)
    <div class="invalid-feedback">
        {{$message}}
    </div>
@enderror
