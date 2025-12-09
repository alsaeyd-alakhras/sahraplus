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

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.name_ar') }}" :value="$tax->name_ar" name="name_ar"
                            placeholder="{{ __('admin.name_ar') }}" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.name_en') }}" :value="$tax->name_en" name="name_en"
                            placeholder="{{ __('admin.name_en') }}" required />
                    </div>
                    <div class="mb-4 col-md-4">

                        <x-form.selectkey required label="{{ __('admin.tax_type') }}" name="tax_type" :selected="$tax->tax_type ?? 'percentage'"
                            :options="[
                                'fixed' => __('admin.fixed'),
                                'percentage' => __('admin.percentage'),
                            ]" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.tax_code') }}" :value="$tax->tax_code" name="tax_code"
                            placeholder="{{ __('admin.tax_code') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.tax_rate') }}" :value="$tax->tax_rate"
                            name="tax_rate" placeholder="{{ __('admin.tax_rate') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.min_amount') }}" :value="$tax->min_amount"
                            name="min_amount" placeholder="{{ __('admin.min_amount') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.max_amount') }}" :value="$tax->max_amount"
                            name="max_amount" placeholder="{{ __('admin.max_amount') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.sort_order') }}" :value="$tax->sort_order"
                            name="sort_order" placeholder="{{ __('admin.sort_order') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="date" label="{{ __('admin.effective_from') }}" :value="$tax->effective_from ? $tax->effective_from->format('Y-m-d') : ''"
                            name="effective_from" placeholder="{{ __('admin.effective_from') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="date" label="{{ __('admin.effective_until') }}" name="effective_until"
                            :value="$tax->effective_until ? $tax->effective_until->format('Y-m-d') : ''" placeholder="{{ __('admin.effective_until') }}" />
                    </div>


                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.applicable_countries') }}" :value="$tax->applicable_countries"
                            name="applicable_countries" placeholder="{{ __('admin.applicable_countries') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.applicable_plans') }}" :value="$tax->applicable_plans"
                            name="applicable_plans" placeholder="{{ __('admin.applicable_plans') }}" />
                    </div>
                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.compound_tax') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="compound_tax" value="0">
                            <input class="form-check-input" type="checkbox" id="compound_tax" name="compound_tax"
                                value="1" @checked(old('compound_tax', $tax->compound_tax))>
                        </div>
                    </div>
                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.is_active') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" @checked(old('is_active', $tax->is_active))>
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

                    <button type="button" class="btn btn-danger"
                        id="confirmDeleteBtn">{{ __('admin.Save') }}</button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            const _token = "{{ csrf_token() }}";
            const urlAssetPath = "{{ config('app.asset_url') }}";
        </script>
        <script src="{{ asset('js/custom/mediaPage.js') }}"></script>
        <script src="{{ asset('js/custom/plans.js') }}"></script>
    @endpush
