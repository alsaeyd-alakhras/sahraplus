<div class="row">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
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
    <div class="col-md-12">
        <input type="hidden" name="season_id" value="{{ $season->id }}">
        <input type="hidden" name="view_count" value="{{ $episode->view_count ?? 0 }}">
        {{-- القسم الأول: العناوين --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <!-- عنوان بالعربية -->
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.title_ar') }}" name="title_ar" class="text-right"
                            :value="$episode->title_ar ?? 'الحلقة ' . $episode->episode_number" required readonly placeholder="{{ __('admin.title_ar') }}" />
                    </div>

                    <!-- عنوان بالإنجليزية -->
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.title_en') }}" name="title_en" class="text-left"
                            :value="$episode->title_en ?? 'Episode ' . $episode->episode_number" required readonly placeholder="{{ __('admin.title_en') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثاني: الأوصاف --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <!-- الوصف بالعربية -->
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_ar') }}" name="description_ar"
                            :value="$episode->description_ar" rows="2" placeholder="{{ __('admin.description_ar') }}" />
                    </div>

                    <!-- الوصف بالإنجليزية -->
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.description_en') }}" name="description_en"
                            :value="$episode->description_en" rows="2" placeholder="{{ __('admin.description_en') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثالث: الحالة --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <!-- رقم الحلقة -->
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.episode_number') }}" name="episode_number" :value="$episode->episode_number"
                            type="number" placeholder="1" required readonly />
                        <span id="episode_number_error" class="text-danger d-none"></span>
                    </div>
                    <!-- حالة النشر -->
                    <div class="col-md-6">
                        <x-form.selectkey label="{{ __('admin.status') }}" name="status" :selected="$episode->status ?? 'draft'"
                            :options="[
                                'draft' => __('admin.draft'),
                                'published' => __('admin.published'),
                                'archived' => __('admin.archived'),
                            ]" />
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الرابع: التواريخ والإحصاءات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <!-- مدة الحلقة -->
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.duration_minutes') }}" name="duration_minutes" type="number"
                            :value="$episode->duration_minutes ?? ''" placeholder="45" />
                    </div>

                    <!-- تاريخ العرض -->
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.air_date') }}" name="air_date" :value="Carbon\Carbon::parse($episode->air_date)->format('Y-m-d')"
                            type="date" />
                    </div>
                </div>

                <div class="row ">

                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="وقت تخطي المقدمة " :value="$episode->intro_skip_time" name="intro_skip_time"
                            min="0" />

                    </div>

                </div>
            </div>
        </div>

        {{-- القسم الخامس: التصنيفات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <!-- تقييم IMDb -->
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.imdb_rating') }}" name="imdb_rating" type="number"
                            step="0.1" max="10" placeholder="8.5" />
                    </div>
                    <!-- ID TMDB -->
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.tmdb_id') }}" name="tmdb_id" type="number"
                            placeholder="1412" />
                    </div>
                </div>
            </div>
        </div>


        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.video_files') }}</label>
                            <button type="button" id="add-video-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.add') }}
                            </button>
                        </div>

                        <div id="video-rows" class="gap-3 d-grid">
                            @php
                                $oldVideos = old(
                                    'video_files',
                                    isset($episode)
                                        ? $episode->videoFiles
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

                            @if (empty($oldVideos) && !isset($btn_label))
                                @include('dashboard.series.episodes.partials._video_row', [
                                    'i' => 0,
                                    'row' => [],
                                ])
                            @else
                                @foreach ($oldVideos as $i => $row)
                                    @include('dashboard.series.episodes.partials._video_row', [
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
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.subtitles') }}</label>
                            <button type="button" id="add-sub-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.add') }}
                            </button>
                        </div>

                        <div id="sub-rows" class="gap-3 d-grid">
                            @php
                                $oldSubs = old(
                                    'subtitles',
                                    isset($episode)
                                        ? $episode->subtitles->map
                                            ->only(['language','id',  'label', 'file_url', 'is_default'])
                                            ->toArray()
                                        : [],
                                );
                            @endphp

                            @if (empty($oldSubs) && !isset($btn_label))
                                @include('dashboard.series.episodes.partials._subtitle_row', [
                                    'i' => 0,
                                    'row' => [],
                                ])
                            @else
                                @foreach ($oldSubs as $i => $row)
                                    @include('dashboard.series.episodes.partials._subtitle_row', [
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



        {{-- القسم السادس: الصور والروابط --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- بوستر --}}
                    <div class="col-md-6">
                        @php
                            $thumbnail_url = Str::startsWith($episode->thumbnail_url, ['http', 'https']);
                            $thumbnail_url_out = $thumbnail_url ? $episode->thumbnail_url : null;
                        @endphp
                        <x-form.input type="url" label="{{ __('admin.thumbnail_url') }}" :value="$thumbnail_url_out"
                            name="thumbnail_url_out" placeholder="{{ __('admin.thumbnail_url_placeholder') }}" />

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearThumbnailBtn" data-img="#thumbnail_img" data-mode="single"
                                data-input="#thumbnailInput" class="mt-3 btn btn-primary openMediaModal">
                                {{ __('admin.choose_from_media') }}
                            </button>
                            <button type="button" id="clearThumbnailBtn"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($episode->thumbnail_url) ? '' : 'd-none' }}"
                                data-img="#thumbnail_img" data-input="#thumbnailInput">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $episode->thumbnail_full_url }}" alt="thumbnail" id="thumbnail_img"
                                class="{{ !empty($episode->thumbnail_url) ? '' : 'd-none' }}"
                                style="max-height:100px">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- زر الحفظ --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="saveEpisodeBtn">
                {{ $btn_label ?? __('admin.Save') }}
            </button>
        </div>

    </div>
</div>

{{-- مودال الوسائط --}}
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-6 text-2xl font-bold modal-title">📁 {{ __('admin.media_library') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeMediaModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 modal-body">
                <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="file" name="image" id="imageInputMedia" class="mb-2 form-control">
                    <button type="button" id="uploadFormBtn"
                        class="btn btn-primary">{{ __('admin.upload_image') }}</button>
                </form>
                <div id="mediaGrid" class="masonry">
                    {{-- الصور ستُملأ تلقائيًا عبر jQuery --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="selectMediaBtn">{{ __('admin.select') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
    aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.confirm_delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                {{ __('admin.confirm_delete_message') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="closeDeleteModal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    id="confirmDeleteBtn">{{ __('admin.delete') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const urlIndex = "{{ route('dashboard.media.index') }}";
        const urlStore = "{{ route('dashboard.media.store') }}";
        const urlDelete = "{{ route('dashboard.media.destroy', ':id') }}";
        const _token = "{{ csrf_token() }}";
        const urlAssetPath = "{{ config('app.asset_url') }}";
    </script>
    <script src="{{ asset('js/custom/mediaPage.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#episode_number').on('blur', function() {
                if ($(this).val() < 1) {
                    $('#episode_number_error').text('الرقم يجب أن يكون أكبر من 0');
                    $(this).addClass('is-invalid');
                    $('#episode_number_error').removeClass('d-none');
                    $('#saveEpisodeBtn').attr('disabled', true);
                    return;
                } else {
                    $('#episode_number_error').addClass('d-none');
                    $(this).removeClass('is-invalid');
                    $('#saveEpisodeBtn').attr('disabled', false);
                }

                $.ajax({
                    url: "{{ route('dashboard.episodes.checkEpisodNumber') }}",
                    type: 'GET',
                    data: {
                        episode_number: $(this).val(),
                        id: '{{ $episode->id }}',
                        season_id: '{{ $season->id }}'
                    },
                    success: function(response) {
                        if (response.status == true) {
                            $('#episode_number_error').addClass('d-none');
                            $('#saveEpisodeBtn').attr('disabled', false);
                        } else {
                            $('#episode_number_error').removeClass('d-none');
                            $('#episode_number_error').text(response.message);
                            $('#saveEpisodeBtn').attr('disabled', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        toastr.error('حدث خطأ أثناء التحقق من رقم الحلقة');
                    }
                });

            });
        });
    </script>


    <script>
        const episodeVideoRowPartial = "{{ route('dashboard.episodes.videoRowPartial') }}";
        const episodeSubtitleRowPartial = "{{ route('dashboard.episodes.subtitleRowPartial') }}";
    </script>
    <script src="{{ asset('js/custom/episodes.js') }}"></script>
@endpush
