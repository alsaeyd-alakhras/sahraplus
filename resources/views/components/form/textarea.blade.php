@props([
    'value' => '',
    'name',
    'label'=>'',
    'required' => false,
    'rows' => 3,
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

<textarea
    name="{{$name}}"
    rows="{{$rows}}"
    {{$attributes->class([
        'form-control',
        'is-invalid' => $errors->has($name)
    ])}}
>{{old($name,$value)}}</textarea>

{{-- Validation --}}
@error($name)
    <div class="invalid-feedback">
        {{$message}}
    </div>
@enderror
