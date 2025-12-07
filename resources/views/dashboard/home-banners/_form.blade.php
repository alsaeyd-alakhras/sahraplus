<div class="row">
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
        {{-- نوع المحتوى --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.ContentType') }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="content_type" id="content_type" required>
                            <option value="">{{ __('admin.SelectContentType') }}</option>
                            <option value="movie" @selected(old('content_type', $homeBanner->content_type) == 'movie')>{{ __('admin.MovieSingular') }}</option>
                            <option value="series" @selected(old('content_type', $homeBanner->content_type) == 'series')>{{ __('admin.SeriesSingular') }}</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.Content') }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="content_id" id="content_id" required>
                            <option value="">{{ __('admin.SelectContent') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- مكان العرض والإعدادات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.Placement') }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="placement" required>
                            <option value="">{{ __('admin.SelectPlacement') }}</option>
                            @foreach($placementOptions as $key => $label)
                                <option value="{{ $key }}" @selected(old('placement', $homeBanner->placement) == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.DisplayOrder') }}</label>
                        <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $homeBanner->sort_order ?? 0) }}" min="0">
                        <small class="text-muted">{{ __('admin.SmallerNumbersAppearFirst') }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- الحالة والخيارات --}}
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label d-block">{{ __('admin.Status') }}</label>
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $homeBanner->is_active ?? true))>
                            <label class="form-check-label">{{ __('admin.Active') }}</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_kids" value="0">
                            <input class="form-check-input" type="checkbox" name="is_kids" value="1" @checked(old('is_kids', $homeBanner->is_kids))>
                            <label class="form-check-label">{{ __('admin.KidsContent') }}</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.DisplayDatesOptional') }}</label>
                        <div class="mb-2">
                            <input type="datetime-local" class="form-control" name="starts_at" value="{{ old('starts_at', $homeBanner->starts_at?->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">{{ __('admin.StartDate') }}</small>
                        </div>
                        <div>
                            <input type="datetime-local" class="form-control" name="ends_at" value="{{ old('ends_at', $homeBanner->ends_at?->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">{{ __('admin.EndDate') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                {{ $btn_label ?? __('admin.Save') }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // بيانات الأفلام والمسلسلات من الكنترولر
    const moviesData = @json($movies);
    const seriesData = @json($series);
    const currentContentType = "{{ old('content_type', $homeBanner->content_type) }}";
    const currentContentId = "{{ old('content_id', $homeBanner->content_id) }}";
    const transSelectContent = "{{ __('admin.SelectContent') }}";

    $(document).ready(function() {
        // عند تغيير نوع المحتوى
        $('#content_type').on('change', function() {
            const type = $(this).val();
            const contentSelect = $('#content_id');
            contentSelect.empty().append(`<option value="">${transSelectContent}</option>`);

            if (type === 'movie') {
                moviesData.forEach(movie => {
                    const title = movie.title_ar || movie.title_en || 'N/A';
                    contentSelect.append(`<option value="${movie.id}">${title}</option>`);
                });
            } else if (type === 'series') {
                seriesData.forEach(series => {
                    const title = series.title_ar || series.title_en || 'N/A';
                    contentSelect.append(`<option value="${series.id}">${title}</option>`);
                });
            }
        });

        // تحميل المحتوى الحالي عند التعديل
        if (currentContentType) {
            $('#content_type').trigger('change');
            if (currentContentId) {
                setTimeout(() => {
                    $('#content_id').val(currentContentId);
                }, 100);
            }
        }
    });
</script>
@endpush

