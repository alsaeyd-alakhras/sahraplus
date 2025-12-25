@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $name = 'name_' . app()->getLocale();
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- عنوان + مشاركة --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{__('admin.title_video')}}</label>
                        <input type="text" name="title" class="form-control" required
                            value="{{ old('title', $short->title) }}">
                    </div>
                    {{-- <div class="mb-4 col-md-6">
                        <label class="form-label">{{__('admin.share_url')}} </label>
                        <input type="url" name="share_url" class="form-control"
                            value="{{ old('share_url', $short->share_url) }}">
                    </div> --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{__('admin.video_basic_url')}}</label>
                        <input type="url" name="video_basic_url" class="form-control"
                            value="{{ old('video_basic_url', $short->video_basic_url) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الوصف --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{__('admin.description')}}</label>
                        <textarea class="form-control" name="description" rows="2">{{ old('description', $short->description) }}</textarea>
                    </div>
                    <div class="mb-4 col-md-6">

                        @php
                            $poster_path = Str::startsWith($short->poster_path, ['http', 'https']);
                            $poster_path_out = $poster_path ? $short->poster_path : null;
                        @endphp
                        <x-form.input type="url" label=" {{__('admin.poster') }}" :value="$poster_path_out" name="poster_path_out"
                            placeholder="{{__('admin.click_to_upload')}}" />

                        <input type="text" id="imageInput" name="poster_path"
                            value="{{ old('poster_path', $short->poster_path) }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn1" data-img="#poster_img" data-mode="single"
                                data-input="#imageInput" data-out-input="#poster_path_out" class="mt-3 btn btn-primary openMediaModal">
                               {{__('admin.click_to_upload')}}
                            </button>
                            <button type="button"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($short->poster_path) ? '' : 'd-none' }}"
                                id="clearImageBtn1" data-img="#poster_img" data-input="#imageInput" data-out-input="#poster_path_out">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $short->poster_full_path }}" alt="poster" id="poster_img"
                                class="{{ !empty($short->poster_path) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                    </div>
                </div>
                {{--
                @php
                    $video_out = old('video_path_out');
                    $video_local = old('video_path', $short->video_path);
                    $video_preview = $video_out
                        ? $video_out
                        : ($video_local
                            ? (\Illuminate\Support\Str::startsWith($video_local, ['http', 'https'])
                                ? $video_local
                                : asset('storage/' . ltrim($video_local, '/')))
                            : null);
                @endphp
                <div class="mb-4 col-md-6">
                    <label class="form-label"> {{ __('admin.video_external') }}</label>
                    <input type="url" name="video_path_out" class="form-control" value="{{ $video_out }}">
                    <input type="file" name="videoUpload" class="mt-2 form-control" />
                    <input type="text" name="video_path" id="videoPathLocal" value="{{ $video_local ?? '' }}"
                        class="d-none form-control">
                    @if ($video_preview)
                        <a href="{{ $video_preview }}" target="_blank" class="mt-2 btn btn-sm btn-primary">{{__('admin.view')}}</a>
                    @endif
                </div> --}}
            </div>
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- إعدادات --}}
                    <div class="mb-4 col-md-4">
                        <label class="form-label"> {{__('admin.order')}} </label>
                        <select class="form-control" name="aspect_ratio">
                            <option value="vertical" @selected(old('aspect_ratio', $short->aspect_ratio) == 'vertical')>{{ __('admin.vertical') }}</option>
                            <option value="horizontal" @selected(old('aspect_ratio', $short->aspect_ratio) == 'horizontal')>{{ __('admin.horizontal') }}</option>
                        </select>
                    </div>
                    <div class="mb-4 col-md-4">
                        <label class="form-label">{{__('admin.status')}}</label>
                        <select class="form-control" name="status">
                            <option value="active" @selected(old('status', $short->status) == 'active')>{{__('admin.active')}}</option>
                            <option value="inactive" @selected(old('status', $short->status) == 'inactive')>{{__('admin.inactive')}} </option>
                        </select>
                    </div>
                    <div class="mb-4 col-md-4">
                        <label class="form-label">خيارات العرض</label>
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                @checked(old('is_featured', $short->is_featured))>
                            <label class="form-check-label">{{__('admin.is_featured')}}</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_kids" value="0">
                            <input class="form-check-input" type="checkbox" name="is_kids" value="1"
                                @checked(old('is_kids', $short->is_kids))>
                            <label class="form-check-label">محتوى للأطفال</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- التصنيفات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- Categories --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">
                            {{ __('admin.Movie Category') }}
                        </label>

                        <select name="category_ids[]"    id="category-select" class="form-control select2" multiple
                            data-placeholder="{{ __('admin.select_categories') }}">
                            @foreach ($allCategories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, old('category_ids', $short->categories->pluck('id')->toArray() ?? []))
                                        ? 'selected'
                                        : '' }}>
                                    {{ $category->$name }}
                                </option>
                            @endforeach
                        </select>

                        <span class="text-muted d-block mt-2">
                            {{ __('admin.select_at_least_one_category') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {{-- ملفات الفيديو الثانوية (مثل الأفلام) --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.video_files') }}</label>
                            {{-- <button type="button" id="add-video-row" class="btn btn-dark btn-sm">+
                                {{ __('admin.add') }}</button> --}}
                        </div>

                        <div id="video-rows" class="gap-3 d-grid">
                            @php
                                $oldVideos = old(
                                    'video_files',
                                    isset($short)
                                        ? $short->videoFiles
                                            ->map(function ($vf) {
                                                return [
                                                    'id' => $vf->id,
                                                    'video_type' => $vf->video_type,
                                                    'quality' => $vf->quality,
                                                    'file_url' => $vf->file_url,
                                                    'format' => $vf->format,
                                                ];
                                            })
                                            ->toArray()
                                        : [],
                                );
                            @endphp

                            @if (empty($oldVideos))
                                @include('dashboard.shorts.partials._video_row', ['i' => 0, 'row' => []])
                            @else
                                @foreach ($oldVideos as $i => $row)
                                    @include('dashboard.shorts.partials._video_row', [
                                        'i' => $i,
                                        'row' => $row,
                                    ])
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
</div>

@include('layouts.partials.dashboard.mediamodel')

@push('scripts')
    <script>
        let person_duplicate = "{{ __('admin.person_duplicate') }}";
        const form_type = "{{ isset($btn_label) }}";
        const videoRowPartial = "{{ route('dashboard.shorts.videoRowPartial') }}";

    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/custom/shorts.js') }}"></script>
    <script>
                $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                minimumResultsForSearch: 0, // إجبار ظهور البحث
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });
        });
    </script>

@endpush
