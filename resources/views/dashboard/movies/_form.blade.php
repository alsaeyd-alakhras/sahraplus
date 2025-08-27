<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <!-- Account -->
            <div class="card-body">
                {{-- ممكن تحط عنوان/وصف هنا لو حاب --}}
            </div>
            <!-- /Account -->
        </div>

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

                    {{-- السلاج/المعرف --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="المعرف (Slug)" :value="$movie->slug" name="slug"
                                      placeholder="movie-slug" required />
                    </div>

                    {{-- التريلر --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="رابط التريلر" :value="$movie->trailer_url" name="trailer_url"
                                      placeholder="https://youtube.com/..." />
                    </div>

                    {{-- الأوصاف --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label">الوصف (عربي)</label>
                        <textarea class="form-control" name="description_ar" rows="4"
                                  placeholder="نبذة عن الفيلم...">{{ $movie->description_ar }}</textarea>
                    </div>
                    <div class="mb-4 col-md-6">
                        <label class="form-label">الوصف (إنجليزي)</label>
                        <textarea class="form-control" name="description_en" rows="4"
                                  placeholder="Movie synopsis...">{{ $movie->description_en }}</textarea>
                    </div>

                    {{-- تاريخ/مدة/تقييم --}}
                    <div class="mb-4 col-md-4">
                        <x-form.input type="date" label="تاريخ الإصدار" :value="$movie->release_date?->format('Y-m-d')"
                                      name="release_date" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" label="المدة بالدقائق" :value="$movie->duration_minutes"
                                      name="duration_minutes" placeholder="120" min="0" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" step="0.1" label="تقييم IMDb (0-10)" :value="$movie->imdb_rating"
                                      name="imdb_rating" placeholder="7.8" min="0" max="10" />
                    </div>

                    {{-- التصنيف/اللغة/الدولة --}}
                    <div class="mb-4 col-md-4">
                        <x-form.input label="التصنيف العمري" :value="$movie->content_rating" name="content_rating"
                                      placeholder="PG-13, R ..." />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input label="اللغة" :value="$movie->language ?? 'ar'" name="language"
                                      placeholder="ar, en ..." />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.input label="بلد الإنتاج (رمز 2)" :value="$movie->country" name="country"
                                      placeholder="SA, EG, PS ..." />
                    </div>

                    {{-- الحالة --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">حالة النشر</label>
                        @php $curStatus = $movie->status ?? 'draft'; @endphp
                        <select class="form-control" name="status" required>
                            <option value="draft"     @selected($curStatus === 'draft')>مسودة</option>
                            <option value="published" @selected($curStatus === 'published')>منشور</option>
                            <option value="archived"  @selected($curStatus === 'archived')>مؤرشف</option>
                        </select>
                    </div>

                    {{-- مميز --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">مميز</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                   @checked($movie->is_featured)>
                            <label class="form-check-label" for="is_featured">عرض كفيلم مميز</label>
                        </div>
                    </div>

                    {{-- الروابط/الرفع: بوستر وخلفية --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="رابط البوستر" :value="$movie->poster_url" name="poster_url"
                                      placeholder="أو ارفع ملفًا بالأسفل" />
                        <input type="file" name="posterUpload" class="form-control mt-2" />
                        @if(!empty($movie->poster_url))
                            <div class="mt-2">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($movie->poster_url) }}"
                                     alt="poster" style="max-height:100px">
                            </div>
                        @endif
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="رابط الخلفية" :value="$movie->backdrop_url" name="backdrop_url"
                                      placeholder="أو ارفع ملفًا بالأسفل" />
                        <input type="file" name="backdropUpload" class="form-control mt-2" />
                        @if(!empty($movie->backdrop_url))
                            <div class="mt-2">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($movie->backdrop_url) }}"
                                     alt="backdrop" style="max-height:100px">
                            </div>
                        @endif
                    </div>

                    {{-- TMDB وعداد المشاهدات (اختياري) --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input label="TMDB ID" :value="$movie->tmdb_id" name="tmdb_id" placeholder="مثال: 550" />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="عدد المشاهدات" :value="$movie->view_count" name="view_count"
                                      placeholder="0" min="0" />
                    </div>
                </div>
            </div>
        </div>

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
