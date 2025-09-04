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
                        <x-form.input
                            label="{{ __('admin.title_ar') }}"
                            name="title_ar"
                            :value="$episode->title_ar"
                            required
                            placeholder="{{ __('admin.title_ar') }}" />
                    </div>

                    <!-- عنوان بالإنجليزية -->
                    <div class="col-md-6">
                        <x-form.input
                            label="{{ __('admin.title_en') }}"
                            name="title_en"
                            :value="$episode->title_en"
                            required
                            placeholder="{{ __('admin.title_en') }}" />
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
                        <x-form.textarea
                            label="{{ __('admin.description_ar') }}"
                            name="description_ar"
                            :value="$episode->description_ar"
                            rows="2"
                            placeholder="{{ __('admin.description_ar') }}" />
                    </div>

                    <!-- الوصف بالإنجليزية -->
                    <div class="col-md-6">
                        <x-form.textarea
                            label="{{ __('admin.description_en') }}"
                            name="description_en"
                            :value="$episode->description_en"
                            rows="2"
                            placeholder="{{ __('admin.description_en') }}" />
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
                        <x-form.input label="{{ __('admin.episode_number') }}" name="episode_number" :value="$episode->episode_number" type="number" placeholder="1" required />
                        <span id="episode_number_error" class="text-danger d-none"></span>
                    </div>
                    <!-- حالة النشر -->
                    <div class="col-md-6">
                        <x-form.selectkey
                            label="{{ __('admin.status') }}"
                            name="status"
                            :selected="$episode->status ?? 'draft'"
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
                        <x-form.input
                            label="{{ __('admin.duration_minutes') }}"
                            name="duration_minutes"
                            type="number"
                            :value="$episode->duration_minutes ?? ''"
                            placeholder="45" />
                    </div>

                    <!-- تاريخ العرض -->
                    <div class="col-md-6">
                        <x-form.input
                            label="{{ __('admin.air_date') }}"
                            name="air_date"
                            :value="Carbon\Carbon::parse($episode->air_date)->format('Y-m-d')"
                            type="date" />
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
                        <x-form.input
                            label="{{ __('admin.imdb_rating') }}"
                            name="imdb_rating"
                            type="number"
                            step="0.1"
                            max="10"
                            placeholder="8.5" />
                    </div>
                    <!-- ID TMDB -->
                    <div class="col-md-6">
                        <x-form.input
                            label="{{ __('admin.tmdb_id') }}"
                            name="tmdb_id"
                            type="number"
                            placeholder="1412" />
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
                        <x-form.input type="url" label="رابط البوستر" :value="$episode->thumbnail_url" name="thumbnail_url_out"
                            placeholder="أو اختر من الوسائط" />
                        <input type="text" id="thumbnailInput" name="thumbnail_url" value="{{ $episode->thumbnail_url }}"
                            class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearThumbnailBtn" data-img="#thumbnail_img" data-mode="single"
                                data-input="#thumbnailInput" class="mt-3 btn btn-primary openMediaModal">
                                اختر من الوسائط
                            </button>
                            <button type="button" id="clearThumbnailBtn"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($episode->thumbnail_url) ? '' : 'd-none' }}"
                                data-img="#thumbnail_img" data-input="#thumbnailInput">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $episode->thumbnail_full_url }}" alt="thumbnail" id="thumbnail_img"
                                class="{{ !empty($episode->thumbnail_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- زر الحفظ --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="saveEpisodeBtn">
                {{ $btn_label ?? 'حفظ' }}
            </button>
        </div>

    </div>
</div>
{{-- مودال الوسائط --}}
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-6 text-2xl font-bold modal-title">📁 مكتبة الوسائط</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeMediaModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 modal-body">
                <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="file" name="image" id="imageInputMedia" class="mb-2 form-control">
                    <button type="button" id="uploadFormBtn" class="btn btn-primary">رفع صورة</button>
                </form>
                <div id="mediaGrid" class="masonry">
                    {{-- الصور ستُملأ تلقائيًا عبر jQuery --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="selectMediaBtn">اختيار</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
    aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذه الصورة؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="closeDeleteModal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">نعم، حذف</button>
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
                        id : '{{ $episode->id }}',
                        season_id : '{{ $season->id }}'
                    },
                    success: function(response) {
                        if(response.status == true){
                            $('#episode_number_error').addClass('d-none');
                            $('#saveEpisodeBtn').attr('disabled', false);
                        }else{
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
@endpush
