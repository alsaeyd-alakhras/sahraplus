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
                                <x-form.input label="اسم الموقع (عربي)" name="site_name_ar" :value="$setting['site_name_ar'] ?? ''" required />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="اسم الموقع (إنجليزي)" name="site_name_en" :value="$setting['site_name_en'] ?? ''" required />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="البريد الإلكتروني" name="site_email" :value="$setting['site_email']?? ''" required />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="الهاتف" name="site_phone" :value="$setting['site_phone']?? ''" />
                            </div>
                            <div class="mb-4 col-md-12">
                                <x-form.input label="العنوان" name="site_address" :value="$setting['site_address']?? ''" />
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
                                    :value="$setting['logo_url'] ?? ''"
                                    placeholder="أو ارفع ملفًا بالأسفل" />
                                <input type="file" name="logoUpload" class="mt-2 form-control" />
                                @if(!empty($setting['logo_url'] ?? null))
                                <div class="mt-2">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($setting['logo_url']) }}"
                                        alt="logo" style="max-height:50px">
                                </div>
                                @endif
                            </div>

                            <div class="mb-4 col-md-6">
                                <x-form.input
                                    label="رابط الأيقونة (Favicon)"
                                    name="favicon_url"
                                    :value="$setting['favicon_url'] ?? ''"
                                    placeholder="أو ارفع ملفًا بالأسفل" />
                                <input type="file" name="faviconUpload" class="mt-2 form-control" />
                                @if(!empty($setting['favicon_url'] ?? null))
                                <div class="mt-2">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($setting['favicon_url']) }}"
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
                                <x-form.input label="الدولة الافتراضية (رمز بلد 2 حروف)" name="default_country" :value="$setting['default_country']?? ''" placeholder="SA, EG ..." />
                            </div>
                            <div class="mb-4 col-md-4">
                                <x-form.input label="العملة الافتراضية (3 أحرف)" name="default_currency" :value="$setting['default_currency']?? ''" placeholder="SAR, EGP ..." />
                            </div>
                            <div class="mb-4 col-md-4">
                                <x-form.input label="المنطقة الزمنية (timezone)" name="timezone" :value="$setting['timezone']?? ''" placeholder="Asia/Riyadh" />
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
                                        @checked((bool)($setting['maintenance_mode'] ?? false))>
                                    <label class="form-check-label" for="maintenance_mode">تفعيل وضع الصيانة</label>
                                </div>
                            </div>
                            <div class="mb-4 col-md-8">
                                <label class="form-label">رسالة الصيانة</label>
                                <textarea class="form-control"
                                    name="maintenance_message"
                                    rows="3"
                                    placeholder="نص يظهر للزوار عند تفعيل وضع الصيانة">{{ $setting['maintenance_message'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="mb-3 border shadow card border-1">
                    <div class="card-body">
                        <h5 class="mb-3">روابط التواصل الاجتماعي</h5>
                        <div class="row">
                            <div class="mb-4 col-md-6">
                                <x-form.input label="Facebook" name="facebook_url" :value="$setting['facebook_url']?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="Twitter/X" name="twitter_url" :value="$setting['twitter_url']?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="Instagram" name="instagram_url" :value="$setting['instagram_url']?? ''" />
                            </div>
                            <div class="mb-4 col-md-6">
                                <x-form.input label="YouTube" name="youtube_url" :value="$setting['youtube_url']?? ''" />
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
