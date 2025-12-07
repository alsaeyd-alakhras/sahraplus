<div class="row">
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/movies.css') }}">
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
        $locale = app()->getLocale();
    @endphp

    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- العناوين --}}
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.Name_ar') }}" :value="old('name_ar', $sub->name_ar)" name="name_ar" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.Name_en') }}" :value="old('name_en', $sub->name_en)" name="name_en" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الأوصاف --}}
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.desecription_ar') }}" :value="old('description_ar', $sub->description_ar)"
                            name="description_ar" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.desecription_en') }}" :value="old('description_en', $sub->description_en)"
                            name="description_en" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-3">
                        <x-form.input type="number" label="{{ __('admin.price') }}" :value="old('price', $sub->price)" name="price" />
                    </div>

                    <div class="col-md-3">
                        <x-form.input label="{{ __('admin.currency') }}" :value="old('currency', $sub->currency)" name="currency" />
                    </div>

                    <div class="col-md-3">
                        <x-form.input type="number" placeholder="30" min="0"
                            label="{{ __('admin.trial_days') }}" :value="old('trial_days', $sub->trial_days)" name="trial_days" />
                    </div>

                    <div class="col-md-3">
                        <x-form.input type="number" placeholder="4" min="0"
                            label="{{ __('admin.max_profiles') }}" :value="old('max_profiles', $sub->max_profiles)" name="max_profiles" />
                    </div>

                    <div class="col-md-3">
                        <x-form.input type="number" placeholder="3" min="0"
                            label="{{ __('admin.max_devices') }}" :value="old('max_devices', $sub->max_devices)" name="max_devices" />
                    </div>


                    <div class="col-md-3">
                        <x-form.input type="number" placeholder="1" min="0"
                            label="{{ __('admin.Sort_order') }}" :value="old('sort_order', $sub->sort_order ?? 0)" name="sort_order" min="0" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.billing_period') }}" name="billing_period"
                            :selected="$sub->billing_period ?? 'monthly'" :options="$billing_periodOptions" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <x-form.selectkey label="{{ __('admin.video_quality') }}" name="video_quality"
                            :selected="$sub->video_quality ?? 'hd'" :options="$video_qualityOptions" />
                    </div>

                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.download_enabled') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="download_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="download_enabled"
                                name="download_enabled" value="1" @checked(old('download_enabled', $sub->download_enabled))>
                        </div>
                    </div>

                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.ads_enabled') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="ads_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="ads_enabled" name="ads_enabled"
                                value="1" @checked(old('ads_enabled', $sub->ads_enabled))>
                        </div>
                    </div>

                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.live_tv_enabled') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="live_tv_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="live_tv_enabled"
                                name="live_tv_enabled" value="1" @checked(old('live_tv_enabled', $sub->live_tv_enabled))>
                        </div>
                    </div>

                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.is_popular') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_popular" value="0">
                            <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular"
                                value="1" @checked(old('is_popular', $sub->is_popular))>
                        </div>
                    </div>

                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.is_active') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" @checked(old('is_active', $sub->is_active))>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- Cast (movie_cast) --}}
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.plan_limitations') }}</label>
                            <button type="button" id="add-cast-sub-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.Create') }}
                            </button>
                        </div>

                        {{-- المختار حالياً --}}
                        <div id="cast-selected" class="mb-2 d-none">
                            <div class="flex-wrap gap-2 d-flex"></div>
                            <hr class="mt-2 mb-3">
                        </div>

                        {{-- صفوف التحرير --}}
                        <div id="cast-rows" class="gap-3 d-grid">
                            @php
                                $oldCast = old(
                                    'cast',
                                    isset($sub)
                                        ? $sub->limitations
                                            ->map(function ($p) {
                                                return [
                                                    'id' => $p->id,
                                                    'plan_id' => $p->plan_id,
                                                    'limitation_type' => $p->limitation_type,
                                                    'limitation_key' => $p->limitation_key,
                                                    'limitation_value' => $p->limitation_value,
                                                    'limitation_unit' => $p->limitation_unit,
                                                    'description_ar' => $p->description_ar,
                                                    'description_en' => $p->description_en,
                                                ];
                                            })
                                            ->toArray()
                                        : [],
                                );

                            @endphp

                            @forelse($oldCast as $i => $row)
                                @include('dashboard.subscription_plans.partials._cast_row', [
                                    'i' => $i,
                                    'row' => $row,
                                ])
                            @empty
                                @include('dashboard.subscription_plans.partials._cast_row', [
                                    'i' => 0,
                                    'row' => [],
                                ])
                            @endforelse
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

{{-- مودال الوسائط --}}
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-6 text-2xl font-bold modal-title">{{ __('admin.media') }} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeMediaModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 modal-body">
                <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="file" name="image" id="imageInputMedia" class="mb-2 form-control">
                    <button type="button" id="uploadFormBtn"
                        class="btn btn-primary">{{ __('admin.upload') }}</button>
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
                <h5 class="modal-title">{{ __('admin.Delete Confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                {{ __('admin.Are you sure?') }}
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="closeDeleteModal">إلغاء</button>

                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('admin.Save') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // media
        const urlIndex = "{{ route('dashboard.media.index') }}";
        const urlStore = "{{ route('dashboard.media.store') }}";
        const urlDelete = "{{ route('dashboard.media.destroy', ':id') }}";
        const subRowPartial = "{{ route('dashboard.movies.subRowPartial') }}";
        const _token = "{{ csrf_token() }}";
        const urlAssetPath = "{{ config('app.asset_url') }}";
    </script>
    <script src="{{ asset('js/custom/mediaPage.js') }}"></script>
    <script src="{{ asset('js/custom/movies.js') }}"></script>
@endpush
