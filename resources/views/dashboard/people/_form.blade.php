<div class="row">
    <div class="mb-4 col-md-6">
        <x-form.input label="الاسم (عربي)" :value="$person->name_ar" name="name_ar" placeholder="محمد..." required autofocus />
    </div>
    <div class="mb-4 col-md-6">
        <x-form.input label="الاسم (إنجليزي)" :value="$person->name_en" name="name_en" placeholder="Mohammad" />
    </div>

    <div class="mb-4 col-md-6">
        <label class="form-label">نبذة (عربي)</label>
        <textarea class="form-control" name="bio_ar" rows="4" placeholder="السيرة الذاتية بالعربية...">{{ $person->bio_ar }}</textarea>
    </div>
    <div class="mb-4 col-md-6">
        <label class="form-label">نبذة (إنجليزي)</label>
        <textarea class="form-control" name="bio_en" rows="4" placeholder="Biography in English...">{{ $person->bio_en }}</textarea>
    </div>

    <div class="mb-4 col-md-4">
        <x-form.input type="date" label="تاريخ الميلاد" :value="$person->birth_date?->format('Y-m-d')" name="birth_date" />
    </div>
    <div class="mb-4 col-md-4">
        <x-form.input label="مكان الولادة" :value="$person->birth_place" name="birth_place" />
    </div>
    <div class="mb-4 col-md-4">
        <x-form.input label="الجنسية" :value="$person->nationality" name="nationality" />
    </div>

    <div class="mb-4 col-md-4">
        <label class="form-label d-block">الجنس</label>
        @php $g = $person->gender; @endphp
        <select name="gender" class="form-control">
            <option value="" @selected(!$g)>غير محدد</option>
            <option value="male"   @selected($g==='male')>ذكر</option>
            <option value="female" @selected($g==='female')>أنثى</option>
        </select>
    </div>

    <div class="mb-4 col-md-8">
        <x-form.input label="مشهور بـ (افصل بفواصل)" :value="is_array($person->known_for) ? implode(',', $person->known_for) : ($person->known_for ?? '')" name="known_for" placeholder="ممثل, مخرج, كاتب..." />
    </div>

    <div class="mb-4 col-md-6">
        <x-form.input label="رابط الصورة" :value="$person->photo_url" name="photo_url" placeholder="أو ارفع ملفًا" />
        <input type="file" name="photoUpload" class="form-control mt-2" />
        @if(!empty($person->photo_url))
            <div class="mt-2">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($person->photo_url) }}" alt="photo" style="max-height:100px">
            </div>
        @endif
    </div>

    <div class="mb-4 col-md-6">
        <x-form.input label="TMDB ID" :value="$person->tmdb_id" name="tmdb_id" />
    </div>

    {{-- ✅ حالة النشاط --}}
    <div class="mb-4 col-md-6">
        <label class="form-label d-block">الحالة</label>
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($person->is_active)>
            <label class="form-check-label" for="is_active">نشط</label>
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
