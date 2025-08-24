<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <!-- Account -->
            <div class="card-body">
               
            </div>
            <!-- /Account -->
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="اسم الدولة بالعربي" :value="$country->name_ar" name="name_ar" placeholder="فلسطين ...." required
                            autofocus />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input  label="اسم الدولة بالانجليزي " :value="$country->name_en" name="name_en"
                            placeholder="palestine" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input  label="كود الدولة" :value="$country->code" name="code"
                            placeholder="code" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input  label="ديال الكود" :value="$country->dial_code" name="dial_code"
                            placeholder="Dial Code" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input  label="العملة" :value="$country->currency" name="currency"
                            placeholder="العملات" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input  label="رابط علم الدولة" :value="$country->flag_url" name="flag_url"
                            placeholder="رابط العلم"  />
                    </div>

                    {{-- ✅ حقل حالة النشاط --}}
    <div class="mb-4 col-md-6">
        <label class="form-label d-block">الحالة</label>
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0"> {{-- لضمان وصول القيمة عند إلغاء التفعيل --}}
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                @checked($country->is_active)>
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
    @push('scripts')
        <script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
        <script>
            $(document).ready(function() {
                // عند تغيير حالة Master Checkbox
                $('.master-checkbox').on('change', function() {
                    // الحصول على المجموعة المرتبطة بـ Master Checkbox
                    const targetClass = $(this).data('target');

                    // تحديد/إلغاء تحديد جميع الخيارات الفرعية
                    $(`.${targetClass}`).prop('checked', $(this).prop('checked'));
                });
            });
        </script>
    @endpush
</div>
