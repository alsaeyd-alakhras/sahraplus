<x-dashboard-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
    @endpush

    <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3 border shadow card border-1">
                    <div class="card-body">
                        <h5 class="mb-3">المعلومات العامة</h5>
                        <div class="row">
                            <div class="mb-4 col-md-6">
                                <x-form.input label="اسم الموقع (عربي)" name="site_name_ar" :value="$settings['site_name_ar'] ?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="اسم الموقع (إنجليزي)" name="site_name_en" :value="$settings['site_name_en'] ?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="البريد الإلكتروني" name="site_email" :value="$settings['site_email']?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="الهاتف" name="site_phone" :value="$settings['site_phone']?? ''" />
                            </div>
                            <div class="mb-4 col-md-12">
                                <x-form.input label="العنوان" name="site_address" :value="$settings['site_address']?? ''" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 border shadow card border-1">
                    <div class="card-body">
                        <h5 class="mb-3">الواجهة والشعار</h5>
                        <div class="row">
                            <div class="mb-4 col-md-6">
                                <x-form.input
                                    label="رابط الشعار"
                                    name="logo_url"
                                    :value="$settings['logo_url'] ?? ''"
                                    placeholder="أو ارفع ملفًا بالأسفل" />
                                <input type="file" name="logoUpload" class="mt-2 form-control" />
                                @if(!empty($settings['logo_url'] ?? null))
                                <div class="mt-2">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['logo_url']) }}"
                                        alt="logo" style="max-height:50px">
                                </div>
                                @endif
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.input
                                    label="رابط الأيقونة (Favicon)"
                                    name="favicon_url"
                                    :value="$settings['favicon_url'] ?? ''"
                                    placeholder="أو ارفع ملفًا بالأسفل" />
                                <input type="file" name="faviconUpload" class="mt-2 form-control" />
                                @if(!empty($settings['favicon_url'] ?? null))
                                <div class="mt-2">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['favicon_url']) }}"
                                        alt="favicon" style="max-height:32px">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mb-3 border shadow card border-1">
                    <div class="card-body">
                        <h5 class="mb-3">إعدادات افتراضية</h5>
                        <div class="row">
                            <div class="mb-4 col-md-4">
                                <x-form.input label="الدولة الافتراضية (رمز بلد 2 حروف)" name="default_country" :value="$settings['default_country']?? ''" placeholder="SA, EG ..." />
                            </div>
                            <div class="mb-4 col-md-4">
                                <x-form.input label="العملة الافتراضية (3 أحرف)" name="default_currency" :value="$settings['default_currency']?? ''" placeholder="SAR, EGP ..." />
                            </div>
                            <div class="mb-4 col-md-4">
                                <x-form.input label="المنطقة الزمنية (timezone)" name="timezone" :value="$settings['timezone']?? ''" placeholder="Asia/Riyadh" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 border shadow card border-1">
                    <div class="card-body">
                        <h5 class="mb-3">وضع الصيانة</h5>
                        <div class="row">
                            <div class="mb-4 col-md-4">
                                <label class="form-label d-block">الحالة</label>
                                <input type="hidden" name="maintenance_mode" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        id="maintenance_mode"
                                        name="maintenance_mode"
                                        value="1"
                                        @checked((bool)($settings['maintenance_mode'] ?? false))>
                                    <label class="form-check-label" for="maintenance_mode">تفعيل وضع الصيانة</label>
                                </div>
                            </div>
                            <div class="mb-4 col-md-8">
                                <label class="form-label">رسالة الصيانة</label>
                                <textarea class="form-control"
                                    name="maintenance_message"
                                    rows="3"
                                    placeholder="نص يظهر للزوار عند تفعيل وضع الصيانة">{{ $settings['maintenance_message'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="mb-3 border shadow card border-1">
                    <div class="card-body">
                        <h5 class="mb-3">روابط التواصل الاجتماعي</h5>
                        <div class="row">
                            <div class="mb-4 col-md-6">
                                <x-form.input label="Facebook" name="facebook_url" :value="$settings['facebook_url']?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="Twitter/X" name="twitter_url" :value="$settings['twitter_url']?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="Instagram" name="instagram_url" :value="$settings['instagram_url']?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="YouTube" name="youtube_url" :value="$settings['youtube_url']?? ''" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 border shadow card border-1">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                {{ $btn_label ?? 'تحديث' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-dashboard-layout>
