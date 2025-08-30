<div class="row">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
    @endpush
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- العناوين --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="عنوان الفيلم (عربي)" :value="$movie->title_ar" name="title_ar"
                            placeholder="مثال: الطريق إلى القدس" required autofocus />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="عنوان الفيلم (إنجليزي)" :value="$movie->title_en" name="title_en"
                            placeholder="Movie Title (EN)" />
                    </div>



                    {{-- الأوصاف --}}
                    <div class="mb-4 col-md-6">
                        <x-form.textarea label="الوصف (عربي)" name="description_ar" rows="2" :value="$movie->description_ar"
                            placeholder="نبذة عن الفيلم..." />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.textarea label="الوصف (En)" name="description_en" rows="2" :value="$movie->description_en"
                            placeholder="نبذة عن الفيلم..." />
                    </div>

                    {{-- الحالة --}}
                    <div class="mb-4 col-md-6">
                        <x-form.selectkey label="حالة النشر" name="status" required :selected="$movie->status ?? 'draft'"
                            :options="$statusOptions" />
                    </div>

                    {{-- مميز --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">مميز</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" @checked($movie->is_featured)>
                            <label class="form-check-label" for="is_featured">عرض كفيلم مميز</label>
                        </div>
                    </div>
                    {{-- التريلر --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="رابط التريلر" :value="$movie->trailer_url" name="trailer_url"
                            placeholder="https://youtube.com/..." />
                    </div>

                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- تاريخ/مدة/تقييم --}}
                    <div class="mb-4 col-md-4">
                        <x-form.input type="date" label="تاريخ الإصدار" :value="$movie->release_date?->format('Y-m-d')" name="release_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="المدة بالدقائق" :value="$movie->duration_minutes" name="duration_minutes"
                            placeholder="120" min="0" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" step="0.1" label="تقييم IMDb (0-10)" :value="$movie->imdb_rating"
                            name="imdb_rating" placeholder="7.8" min="0" max="10" />
                    </div>

                    {{-- التصنيف/اللغة/الدولة --}}
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="التصنيف العمري" name="content_rating" :selected="$movie->content_rating ?? 'G'"
                            :options="$contentRatingOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="اللغة" name="language" :selected="$movie->language ?? 'ar'" :options="$languageOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="بلد الإنتاج" name="country" :selected="$movie->country" :options="$countries" />
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الروابط/الرفع: بوستر وخلفية --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="url" label="رابط البوستر" :value="$movie->poster_url" name="poster_url_out"
                            placeholder="أو اختر من الوسائط" />
                        <input type="text" id="imageInput" name="poster_url" value="{{ $movie->poster_url }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn1"
                                data-img="#poster_img"
                                data-mode="single"
                                data-input="#imageInput"
                                class="mt-3 btn btn-primary openMediaModal">
                                اختر من الوسائط
                            </button>
                            <button type="button" class="clear-btn mt-3 btn btn-danger {{ !empty($movie->poster_url) ? '' : 'd-none' }}"
                                id="clearImageBtn1"
                                data-img="#poster_img"
                                data-input="#imageInput"
                                >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $movie->poster_full_url }}"
                                alt="poster" id="poster_img" class="{{ !empty($movie->poster_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>

                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input type="url" label="رابط الخلفية" :value="$movie->backdrop_url" name="backdrop_url_out"
                            placeholder="أو اختر من الوسائط" />
                        <input type="text" id="imageInput2" name="backdrop_url" value="{{ $movie->backdrop_url }}" class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn2"
                                data-img="#backdrop_img"
                                data-mode="single"
                                data-input="#imageInput2"
                                class="mt-3 btn btn-primary openMediaModal">
                                اختر من الوسائط
                            </button>
                            <button type="button" class="clear-btn mt-3 btn btn-danger {{ !empty($movie->backdrop_url) ? '' : 'd-none' }}"
                                id="clearImageBtn2"
                                data-img="#backdrop_img"
                                data-input="#imageInput2"
                                >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $movie->backdrop_full_url }}"
                                alt="backdrop" id="backdrop_img" class="{{ !empty($movie->backdrop_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- TMDB وعداد المشاهدات (اختياري) --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" min="0" label="TMDB ID" :value="$movie->tmdb_id" name="tmdb_id" placeholder="مثال: 550" />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" min="0" label="عدد المشاهدات" :value="$movie->view_count ?? 0" name="view_count"
                            placeholder="0" readonly />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                {{ $btn_label ?? 'أضف' }}
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
@endpush
