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

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">

                    <div class="pt-4 card-body">
                        <div class="row">
                            {{-- Plans --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('admin.sub_plan') }}</label>

                                {{-- الحاوية للمختارة --}}
                                <div id="selected-plans" class="mb-2 d-none">
                                    <div class="flex-wrap gap-2 d-flex"></div>
                                    <hr class="mt-2 mb-3">
                                </div>

                                {{-- مهم: لو ما في اختيار، هالحقل يرسل قيمة فاضية --}}
                                <input type="hidden" name="plan_id"
                                    value="{{ old('plan_id', $planAccess->plan_id ?? '') }}">

                                {{-- الحاوية لكل الخطط --}}
                                <div id="plan-badges" class="flex-wrap gap-2 d-flex">
                                    @foreach ($subscription_plans as $plan)
                                        <label
                                            class="px-3 py-1 mb-2 btn btn-outline-primary rounded-pill {{ old('plan_id', $planAccess->plan_id ?? '') == $plan->id ? 'active' : '' }}"
                                            data-id="{{ $plan->id }}">
                                            <input type="radio" class="d-none" name="plan_id"
                                                value="{{ $plan->id }}"
                                                {{ old('plan_id', $coupon->plan_id ?? '') == $plan->id ? 'checked' : '' }}>
                                            {{ app()->getLocale() === 'ar' ? $plan->name_ar : $plan->name_en }}
                                        </label>
                                    @endforeach
                                </div>

                                <span class="text-muted">{{ __('admin.select_one_plan') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 col-md-4">

                        <x-form.selectkey label="{{ __('admin.discount_type') }}" name="discount_type"
                            :selected="$coupon->discount_type ?? 'category'" :options="[
                                'fixed' => __('admin.fixed'),
                                'percentage' => __('admin.percentage'),
                            ]" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.code') }}" :value="$coupon->code" name="code"
                            placeholder="{{ __('admin.code') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.discount_value') }}" :value="$coupon->discount_value"
                            name="discount_value" placeholder="{{ __('admin.discount_value') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="date" label="{{ __('admin.starts') }}" :value="$coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : ''"
                            name="starts_at" placeholder="{{ __('admin.starts') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="date" label="{{ __('admin.expires_at') }}" name="expires_at"
                            :value="$coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : ''" placeholder="{{ __('admin.expires_at') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.usage_limit') }}" :value="$coupon->usage_limit"
                            name="usage_limit" placeholder="{{ __('admin.usage_limit') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.usage_limit_per_user') }}" :value="$coupon->usage_limit_per_user"
                            name="usage_limit_per_user" placeholder="{{ __('admin.usage_limit_per_user') }}"
                            required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.times_used') }}" :value="$coupon->times_used"
                            name="times_used" placeholder="{{ __('admin.times_used') }}" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.metadata') }}" :value="$coupon->metadata['coupon_info'] ?? ''" name="coupon_info"
                            placeholder="{{ __('admin.metadata') }}" />

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
</div>