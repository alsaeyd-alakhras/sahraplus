<div class="row">
    {{-- عنوان + مشاركة --}}
    <div class="mb-4 col-md-6">
        <label class="form-label">عنوان الفيديو</label>
        <input type="text" name="title" class="form-control" required
               value="{{ old('title', $short->title) }}">
    </div>
    <div class="mb-4 col-md-6">
        <label class="form-label">رابط المشاركة</label>
        <input type="url" name="share_url" class="form-control"
               value="{{ old('share_url', $short->share_url) }}">
    </div>

    {{-- الوصف --}}
    <div class="mb-4 col-md-12">
        <label class="form-label">الوصف</label>
        <textarea class="form-control" name="description" rows="3">{{ old('description', $short->description) }}</textarea>
    </div>

    {{-- البوستر --}}
    @php
      $poster_out   = old('poster_path_out');
      $poster_local = old('poster_path', $short->poster_path);
      $poster_preview = $poster_out
          ? $poster_out
          : ($poster_local ? ( \Illuminate\Support\Str::startsWith($poster_local,['http','https'])
              ? $poster_local
              : asset('storage/' . ltrim($poster_local,'/')) ) : null);
    @endphp
    <div class="mb-4 col-md-6">
        <label class="form-label">رابط البوستر (خارجي)</label>
        <input type="url" name="poster_path_out" class="form-control" value="{{ $poster_out }}">
        <input type="file" name="posterUpload" class="form-control mt-2" />
        <input type="text" name="poster_path" id="posterPathLocal"
               value="{{ $poster_local ?? '' }}" class="d-none form-control">
        @if($poster_preview)
            <img src="{{ $poster_preview }}" style="max-height:80px" class="mt-2" alt="poster">
        @endif
    </div>

    {{-- الفيديو --}}
    @php
      $video_out   = old('video_path_out');
      $video_local = old('video_path', $short->video_path);
      $video_preview = $video_out
          ? $video_out
          : ($video_local ? ( \Illuminate\Support\Str::startsWith($video_local,['http','https'])
              ? $video_local
              : asset('storage/' . ltrim($video_local,'/')) ) : null);
    @endphp
    <div class="mb-4 col-md-6">
        <label class="form-label">رابط الفيديو (خارجي)</label>
        <input type="url" name="video_path_out" class="form-control" value="{{ $video_out }}">
        <input type="file" name="videoUpload" class="form-control mt-2" />
        <input type="text" name="video_path" id="videoPathLocal"
               value="{{ $video_local ?? '' }}" class="d-none form-control">
        @if($video_preview)
            <a href="{{ $video_preview }}" target="_blank" class="btn btn-sm btn-primary mt-2">استعراض</a>
        @endif
    </div>

    {{-- التصنيفات --}}
    <div class="mb-3 border shadow card border-1">
        <div class="pt-4 card-body">
            <div class="row">
                <div class="col-12">
                    <label class="form-label fw-bold">{{ __('admin.Movie Category') }}</label>

                    @php
                      $selectedCategories = collect(old('category_ids', $short->categories?->pluck('id')->toArray() ?? []))
                          ->filter()->map(fn($v)=>(int)$v)->all();
                    @endphp

                    <div id="category-badges" class="d-flex flex-wrap gap-2">
                        @foreach($allCategories as $category)
                          <label class="btn btn-outline-primary rounded-pill px-3 py-1 mb-2">
                              <input type="checkbox" class="d-none"
                                     name="category_ids[]" value="{{ $category->id }}"
                                     @checked(in_array($category->id, $selectedCategories, true))>
                              {{ $category->name_ar }}
                          </label>
                        @endforeach
                    </div>

                    <span class="text-muted">{{ __('admin.select_at_least_one_category') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- إعدادات --}}
    <div class="mb-4 col-md-4">
        <label class="form-label">نسبة العرض</label>
        <select class="form-control" name="aspect_ratio">
            <option value="vertical"  @selected(old('aspect_ratio', $short->aspect_ratio)=='vertical')>عمودي</option>
            <option value="horizontal" @selected(old('aspect_ratio', $short->aspect_ratio)=='horizontal')>أفقي</option>
        </select>
    </div>
    <div class="mb-4 col-md-4">
        <label class="form-label">الحالة</label>
        <select class="form-control" name="status">
            <option value="active"   @selected(old('status', $short->status)=='active')>نشط</option>
            <option value="inactive" @selected(old('status', $short->status)=='inactive')>غير نشط</option>
        </select>
    </div>
    <div class="mb-4 col-md-4">
        <label class="form-label">مميز</label>
        <div class="form-check form-switch">
            <input type="hidden" name="is_featured" value="0">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                   @checked(old('is_featured', $short->is_featured))>
        </div>
    </div>

    {{-- ملفات الفيديو الثانوية (مثل الأفلام) --}}
    <div class="mb-3 border shadow card border-1">
        <div class="pt-4 card-body">
            <div class="row">
                <div class="col-12">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <label class="fw-semibold">{{ __('admin.video_files') }}</label>
                        <button type="button" id="add-video-row" class="btn btn-dark btn-sm">+ {{ __('admin.add') }}</button>
                    </div>

                    <div id="video-rows" class="gap-3 d-grid">
                        @php
                          $oldVideos = old(
                              'video_files',
                              isset($short)
                                  ? $short->videoFiles->map(function ($vf) {
                                        return [
                                            'video_type' => $vf->video_type,
                                            'quality'    => $vf->quality,
                                            'file_url'   => $vf->file_url,
                                            'format'     => $vf->format,
                                        ];
                                    })->toArray()
                                  : [],
                          );
                        @endphp

                        @if (empty($oldVideos) && !isset($btn_label))
                            @include('dashboard.shorts.partials._video_row', ['i' => 0, 'row' => []])
                        @else
                            @foreach ($oldVideos as $i => $row)
                                @include('dashboard.shorts.partials._video_row', ['i' => $i, 'row' => $row])
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- حفظ --}}
    <div class="mb-3 border shadow card border-1">
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    {{ $btn_label ?? 'أضف' }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// تبديل مظهر أزرار التصنيفات + التأشير على الـcheckbox المخفي
document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('category-badges');
  if (!container) return;

  container.querySelectorAll('label').forEach(function (label) {
    const cb = label.querySelector('input[type="checkbox"]');

    function sync() {
      if (cb.checked) {
        label.classList.add('btn-primary', 'text-white');
        label.classList.remove('btn-outline-primary');
      } else {
        label.classList.remove('btn-primary', 'text-white');
        label.classList.add('btn-outline-primary');
      }
    }
    sync();

    label.addEventListener('click', function (e) {
      if (e.target !== cb) {
        e.preventDefault();
        cb.checked = !cb.checked;
        sync();
      }
    });
  });
});
</script>
@endpush
