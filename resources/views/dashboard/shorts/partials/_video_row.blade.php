@php
    $type = $row['video_type'] ?? 'main';
    $q = $row['quality'] ?? null;
    $url = $row['file_url'] ?? '';
    $fmt = $row['format'] ?? '';
    $id_video = $row['id'] ?? '';

    // الآن: دائماً URL
    $source = 'url';
@endphp

<div class="p-3 rounded border video-row card">
    <input type="hidden" name="video_files[{{ $i }}][id]" class="video-id" value="{{ $id_video }}">

    <div class="row g-3 align-items-end">

        <div class="col-md-6">

            <label class="form-label small fw-bold">{{ __('admin.source') }}</label>

            {{-- دائماً URL --}}
            <input type="hidden" name="video_files[{{ $i }}][source_type]" value="url">

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" checked disabled>
                <label class="form-check-label">{{ __('admin.external_url') }}</label>
            </div>

            {{-- الرابط النصّي فقط --}}
            <input type="text" class="form-control mb-2 video-url"
                name="video_files[{{ $i }}][file_url]" value="{{ $url }}"
                placeholder="{{ __('admin.file_url_placeholder') }}">

            {{-- رفع ملف (معلّق الآن) --}}
            {{--
            <input type="file" class="form-control video-file d-none"
                name="video_files[{{ $i }}][file]"
                accept="video/mp4,video/webm,video/quicktime,video/x-matroska">
            --}}

            <input type="hidden" name="video_files[{{ $i }}][format]" class="video-format"
                value="{{ $fmt }}">

        </div>

        {{-- نوع الفيديو مخفي --}}
        <input type="hidden" class="video-type" name="video_files[{{ $i }}][video_type]" value="main">

        {{-- الجودة --}}
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
        </div>

        {{-- حذف السطر --}}
        <div class="col-12 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-danger btn-sm remove-video-row">
                {{ __('admin.delete_row') }}
            </button>
        </div>

    </div>
</div>
