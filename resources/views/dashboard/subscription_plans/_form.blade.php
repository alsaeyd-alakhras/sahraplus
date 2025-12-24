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
    @php
        $oldCountryPrice = old('countryPrices', isset($sub) ? ($countryPrices ?? null) : []);
        $oldPlanAccess = old('planAccess', isset($sub) ? ($planContentAccess ?? []) : []);
    @endphp

    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- العناوين --}}
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.Name_ar') }}" class="text-right" :value="old('name_ar', $sub->name_ar)"
                            name="name_ar" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.Name_en') }}" class="text-left" :value="old('name_en', $sub->name_en)"
                            name="name_en" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- الأوصاف --}}
                    <div class="col-md-6">
                        <x-form.textarea rows="1" label="{{ __('admin.desecription_ar') }}" class="text-right"
                            :value="old('description_ar', $sub->description_ar)" name="description_ar" />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea rows="1" label="{{ __('admin.desecription_en') }}" class="text-left"
                            :value="old('description_en', $sub->description_en)" name="description_en" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-3">
                        <x-form.input type="number" label="{{ __('admin.price') }}" :value="old('price', $sub->price)" name="price" />
                    </div>

                    <div class="mb-4 col-md-3">
                        <x-form.input label="{{ __('admin.currency') }}" value="SAR" name="currency" readonly
                            class="text-left" />
                    </div>

                    <div class="mb-4 col-md-3">
                        <x-form.input type="number" placeholder="30" min="0"
                            label="{{ __('admin.trial_days') }}" :value="old('trial_days', $sub->trial_days)" name="trial_days" />
                    </div>

                    <div class="mb-4 col-md-3">
                        <x-form.input type="number" placeholder="4" min="0"
                            label="{{ __('admin.max_profiles') }}" :value="old('max_profiles', $sub->max_profiles)" name="max_profiles" />
                    </div>

                    <div class="mb-4 col-md-3">
                        <x-form.input type="number" placeholder="3" min="0"
                            label="{{ __('admin.max_devices') }}" :value="old('max_devices', $sub->max_devices)" name="max_devices" />
                    </div>


                    <div class="mb-4 col-md-3">
                        <x-form.input type="number" placeholder="1" min="0"
                            label="{{ __('admin.Sort_order') }}" :value="old('sort_order', $sub->sort_order ?? 0)" name="sort_order" min="0" />
                    </div>
                    <div class="mb-4 col-md-3">
                        <x-form.selectkey label="{{ __('admin.billing_period') }}" name="billing_period"
                            :selected="$sub->billing_period ?? 'monthly'" :options="$billing_periodOptions" />
                    </div>
                    <div class="mb-4 col-md-3">
                        <x-form.selectkey label="{{ __('admin.video_quality') }}" name="video_quality"
                            :selected="$sub->video_quality ?? 'hd'" :options="$video_qualityOptions" />
                    </div>

                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label d-block">{{ __('admin.is_active') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" @checked(old('is_active', $sub->is_active ?? 1))>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label d-block">{{ __('admin.download_enabled') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="download_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="download_enabled"
                                name="download_enabled" value="1" @checked(old('download_enabled', $sub->download_enabled))>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label d-block">{{ __('admin.ads_enabled') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="ads_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="ads_enabled" name="ads_enabled"
                                value="1" @checked(old('ads_enabled', $sub->ads_enabled))>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label d-block">{{ __('admin.live_tv_enabled') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="live_tv_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="live_tv_enabled"
                                name="live_tv_enabled" value="1" @checked(old('live_tv_enabled', $sub->live_tv_enabled))>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label d-block">{{ __('admin.is_popular') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_popular" value="0">
                            <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular"
                                value="1" @checked(old('is_popular', $sub->is_popular))>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label d-block">{{ __('admin.is_customize') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_customize" value="{{ !empty($oldCountryPrice) && isset($btn_label) ? 1 : 0 }}">
                            <input class="form-check-input" type="checkbox" id="is_customize" name="is_customize"
                                value="1" @checked(old('is_customize', $sub->is_customize)) @checked(!empty($oldCountryPrice) && isset($btn_label))>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div id="countryPrice-section" class="mb[]-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.countryPrices') }}</label>
                            <button type="button" id="add-sub-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.add') }}
                            </button>
                        </div>

                        <div id="sub-rows" class="gap-3 d-grid">
                            @if (empty($oldCountryPrice) && !isset($btn_label))
                                @include('dashboard.subscription_plans.partials._countryPrices_row', [
                                    'i' => 0,
                                    'row' => [],
                                ])
                            @else
                                @foreach ($oldCountryPrice as $i => $row)
                                    @include('dashboard.subscription_plans.partials._countryPrices_row', [
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

        <div id="planAccess-section" class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.PlanContentAccess') }}</label>
                            <button type="button" id="add-planAccess-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.add') }}
                            </button>
                        </div>

                        <div id="planAccess-rows" class="gap-3 d-grid">
                            @if (empty($oldPlanAccess))
                                @include('dashboard.subscription_plans.partials._planAccess_row', [
                                    'i' => 0,
                                    'row' => [],
                                ])
                            @else
                                @foreach ($oldPlanAccess as $i => $row)
                                    @include('dashboard.subscription_plans.partials._planAccess_row', [
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

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                {{ $btn_label ?? __('admin.Save') }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const subtitleRowPartial = "{{ route('dashboard.countryPrice.countryRowPartial') }}";
        const planAccessRowPartial = "{{ route('dashboard.planAccess.planAccessRowPartial') }}";

        const urlGetContents = "{{ route('dashboard.plan_access.getContents') }}";
        const successMessage = "{{ __('admin.select_content') }}";
        const errorMessage = "{{ __('admin.error_loading') }}";
        const loadingMessage = "{{ __('admin.loading') }}...";
    </script>
    <script src="{{ asset('js/custom/subscriptionPlans.js') }}"></script>
@endpush