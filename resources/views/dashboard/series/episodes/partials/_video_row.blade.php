@php
    $type = $row['video_type'] ?? 'main';
    $q = $row['quality'] ?? null;
    $url = $row['file_url'] ?? '';
    $fmt = $row['format'] ?? '';

    // نعتبر المحلي إذا بدأ الرابط بـ /storage/ أو asset('storage')
    $isLocal = Str::startsWith($url, ['/storage/', asset('storage')]);
    $source = $url ? ($isLocal ? 'file' : 'url') : 'file';
@endphp
<div class="p-3 rounded border video-row card">
    <div class="row g-3 align-items-end">
        {{-- اختيار المصدر: ملف أو رابط --}}
        <div class="col-md-6">
            <label class="form-label small fw-bold">{{ __('admin.source') }}</label>
            <input type="hidden" class="source-type" name="video_files[{{ $i }}][source_type]" value="{{ $source }}">

            <div class="gap-3 mb-2 d-flex">
                <div class="form-check form-check-inline">
                    <input class="form-check-input source-toggle" type="radio"
                        name="video_files[{{ $i }}][source_type]" value="file"
                        id="src-file-{{ $i }}" {{ $source == 'file' ? 'checked' : '' }}>
                    <label class="form-check-label"
                        for="src-file-{{ $i }}">{{ __('admin.upload_file') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input source-toggle" type="radio"
                        name="video_files[{{ $i }}][source_type]" value="url"
                        id="src-url-{{ $i }}" {{ $source == 'url' ? 'checked' : '' }}>
                    <label class="form-check-label"
                        for="src-url-{{ $i }}">{{ __('admin.external_url') }}</label>
                </div>
            </div>

            {{-- الرابط النصّي --}}
            <input type="text" class="form-control mb-2 video-url {{ $source == 'file' ? 'd-none' : '' }}"
                name="video_files[{{ $i }}][file_url]" value="{{ $source == 'url' ? $url : '' }}"
                placeholder="{{ __('admin.file_url_placeholder') }}">

            {{-- رفع الملف --}}
            <input type="file" class="form-control video-file {{ $source == 'url' ? 'd-none' : '' }}"
                name="video_files[{{ $i }}][file]"
                accept="video/mp4,video/webm,video/quicktime,video/x-matroska">
            @if ($source == 'file' && $url)
                <a href="{{ $url }}" class="mt-2 btn btn-primary" target="_blank">{{ __('admin.open_file') }}</a>
            @endif

            {{-- مهم جداً: لو ملف محلي موجود مسبقًا وما رفع جديد، نرجع نستخدمه --}}
            <input type="hidden" name="video_files[{{ $i }}][existing_url]"
                value="{{ $source == 'file' ? $url : '' }}">

            <input type="hidden" name="video_files[{{ $i }}][format]" class="video-format"
                value="{{ $fmt }}">
            <div class="form-text">{{ __('admin.format_hint') }}</div>
        </div>

        {{-- نوع الفيديو --}}
        {{-- <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.video_type') }}</label>
            <select name="video_files[{{ $i }}][video_type]" class="form-select video-type">
                <option value="main" {{ $type == 'main' ? 'selected' : '' }}>
                    {{ __('admin.video_type_main') }}
                </option>
                <option value="trailer" {{ $type == 'trailer' ? 'selected' : '' }}>
                    {{ __('admin.video_type_trailer') }}
                </option>
            </select>
        </div> --}}
        <input type="hidden" class="video-type" name="video_files[{{ $i }}][video_type]" value="main">


        {{-- الجودة (لازم تكون فريدة) --}}
        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.quality') }}</label>
            <select name="video_files[{{ $i }}][quality]" class="form-select video-quality">
                <option value="240p" {{ $q == '240p' ? 'selected' : '' }}>240p</option>
                <option value="360p" {{ $q == '360p' ? 'selected' : '' }}>360p</option>
                <option value="480p" {{ $q == '480p' ? 'selected' : '' }}>480p</option>
                <option value="720p" {{ $q == '720p' ? 'selected' : '' }}>720p</option>
                <option value="1080p" {{ $q == '1080p' ? 'selected' : '' }}>1080p</option>
                <option value="4k" {{ $q == '4k' ? 'selected' : '' }}>4k</option>
            </select>
            <div class="mt-1 text-danger small d-none quality-dup">{{ __('admin.quality_already_used') }}</div>
        </div>



        {{-- حذف السطر --}}
        <div class="col-12 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-danger btn-sm remove-video-row">
                {{ __('admin.delete_row') }}
            </button>
        </div>
    </div>
</div>
