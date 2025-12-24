<x-dashboard-layout>
    @php
        $title = 'title_' . app()->getLocale();
        $description = 'description_' . app()->getLocale();
    @endphp
    @push('styles')
        <!-- Page CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
    @endpush
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="mb-6 card">

                <div class="mb-5 text-center user-profile-header d-flex flex-column flex-lg-row text-sm-start">

                    <div class="mt-3 flex-grow-1 mt-lg-5">
                        <div
                            class="gap-4 mx-5 d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start flex-md-row flex-column">
                            <div class="user-profile-info">
                                <h4 class="mb-2 mt-lg-6">{{ @$sub->user->first_name . ' ' . @$sub->user->last_name }}
                                </h4>
                                <p class="text-muted">{{ @$sub->user->email }}</p>
                                <h5 class="text-muted">{{ @$sub->plan->name_ar }}</h5>
                                <p class="text-muted">{{ @$sub->plan->description_ar . '  / ' . @$sub->plan->price  }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <div class="row">
        <!-- Series Details -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card">
                <div class="card-body">
                    <small class="mb-3 card-text text-uppercase text-muted small">{{ __('admin.details') }}</small>
                    <ul class="py-1 list-unstyled">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-movie ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.status') }}:</span>
                            <span>{{ $sub->status }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-language ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.amount') }}:</span>
                            <span>{{ $sub->amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.currency') }}:</span>
                            <span>{{ $sub->currency }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.tax_amount') }}:</span>
                            <span>{{ $sub->tax_amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.discount_amount') }}:</span>
                            <span>{{ $sub->discount_amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.total_amount') }}:</span>
                            <span>{{ $sub->total_amount }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.starts_at') }}:</span>
                            <span>{{ $sub->starts_at }}</span>
                        </li>

                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.ends_at') }}:</span>
                            <span>{{ $sub->ends_at }}</span>
                        </li>


                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.payment_method') }}:</span>
                            <span>{{ $sub->payment_method }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.canceled_at') }}:</span>
                            <span>{{ $sub->canceled_at }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.gateway_response') }}:</span>
                            <span>{{ $sub->gateway_response }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.processed_at') }}:</span>
                            <span>{{ $sub->processed_at }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.failed_at') }}:</span>
                            <span>{{ $sub->failed_at }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.refunded_at') }}:</span>
                            <span>{{ $sub->refunded_at }}</span>
                        </li>

                        <li class="mb-3 d-flex align-items-center">
                            <i class="ti ti-link ti-lg"></i>
                            <span class="mx-2 fw-medium">{{ __('admin.created_at') }}:</span>
                            <span>{{ $sub->created_at }}</span>
                        </li>


                    </ul>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>