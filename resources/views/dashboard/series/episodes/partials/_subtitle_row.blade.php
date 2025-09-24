@php
    $lang      = $row['language'] ?? 'ar';
    $label     = $row['label']    ?? '';
    $url       = $row['url']      ?? '';
    $isDefault = !empty($row['is_default']);

    $isLocal   = Str::startsWith($url, ['/storage/', asset('storage')]);
    $source    = $isLocal ? 'file' : 'url';
@endphp

<div class="p-3 rounded border sub-row card">
    <div class="row g-3 align-items-end">

        {{-- اللغة --}}
        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.language') }}</label>
            <select name="subtitles[{{ $i }}][language]" class="form-select sub-language">
              @foreach(['ar'=>'العربية','en'=>'English','fr'=>'Français','es'=>'Español','de'=>'Deutsch','it'=>'Italiano','tr'=>'Türkçe','fa'=>'فارسی','ur'=>'اردو','ru'=>'Русский','zh'=>'中文','ja'=>'日本語','ko'=>'한국어'] as $code=>$txt)
                <option value="{{ $code }}" {{ $lang==$code?'selected':'' }}>{{ $txt }}</option>
              @endforeach
            </select>
            <div class="mt-1 text-danger small d-none lang-dup">{{ __('admin.language_already_used') }}</div>
        </div>
        {{-- الاسم/الليبل --}}
        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.label') }}</label>
            <input type="text" class="form-control sub-label"
                   name="subtitles[{{ $i }}][label]" value="{{ $label }}"
                   placeholder="{{ __('admin.label_placeholder') }}">
            <div class="mt-1 text-danger small d-none label-dup">{{ __('admin.label_already_used') }}</div>
        </div>

        {{-- اختيار المصدر: ملف أو رابط --}}
        <div class="col-md-6">
            <label class="form-label small fw-bold">{{ __('admin.source') }}</label>

            <div class="d-flex gap-3 mb-2">
              <div class="form-check form-check-inline">
                <input class="form-check-input sub-source" type="radio"
                       name="subtitles[{{ $i }}][source_type]" value="file" id="sub-src-file-{{ $i }}" {{ $source=='file'?'checked':'' }}>
                <label class="form-check-label" for="sub-src-file-{{ $i }}">{{ __('admin.upload_file') }}</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input sub-source" type="radio"
                       name="subtitles[{{ $i }}][source_type]" value="url" id="sub-src-url-{{ $i }}" {{ $source=='url'?'checked':'' }}>
                <label class="form-check-label" for="sub-src-url-{{ $i }}">{{ __('admin.external_url') }}</label>
              </div>
            </div>

            <input type="text" class="form-control mb-2 sub-url {{ $source=='file'?'d-none':'' }}"
                   name="subtitles[{{ $i }}][url]"
                   value="{{ $source=='url' ? $url : '' }}"
                   placeholder="{{ __('admin.subtitle_url_placeholder') }}">

            <input type="file" class="form-control sub-file {{ $source=='url'?'d-none':'' }}"
                   name="subtitles[{{ $i }}][file]" accept=".srt,.vtt,text/vtt,application/x-subrip">

            <input type="hidden" name="subtitles[{{ $i }}][existing_url]" value="{{ $source=='file' ? $url : '' }}">

            <div class="form-text">{{ __('admin.subtitle_hint') }}</div>
        </div>

        {{-- افتراضي + حذف --}}
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="form-check">
              <input class="form-check-input sub-default" type="checkbox"
                     name="subtitles[{{ $i }}][is_default]" value="1" {{ $isDefault ? 'checked' : '' }}>
              <label class="form-check-label">{{ __('admin.default') }}</label>
            </div>

            <button type="button" class="btn btn-outline-danger btn-sm remove-sub-row">
              {{ __('admin.delete_row') }}
            </button>
        </div>

    </div>
</div>
