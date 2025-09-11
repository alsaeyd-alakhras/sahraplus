<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_Country_in_Arabic') }}" :value="$country->name_ar" name="name_ar" placeholder="{{ __('admin.Name_Country_placeholder') }}"
                            required autofocus />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_Country_in_English') }}" :value="$country->name_en" name="name_en"
                            placeholder="{{ __('admin.Name_Country_placeholder') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Zip_Country') }}" :value="$country->code" name="code" placeholder="{{ __('admin.Code') }}" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Dial_Country') }}" :value="$country->dial_code" name="dial_code" placeholder="{{ __('admin.Dial_Country_placeholder') }}"
                            required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Currency') }}" :value="$country->currency" name="currency" placeholder="{{ __('admin.Currency_placeholder') }}" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Flag_url') }}" :value="$country->flag_url" name="flag_url"
                            placeholder="{{ __('admin.Flag_url') }}" />
                    </div>

                    {{-- ✅ حقل حالة النشاط --}}
                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{__('admin.Status')}}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0"> {{-- لضمان وصول القيمة عند إلغاء التفعيل --}}
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" @checked($country->is_active)>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>

                    {{-- ✅ حقل ترتيب العرض --}}
                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="ترتيب العرض" :value="$country->sort_order" name="sort_order"
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
