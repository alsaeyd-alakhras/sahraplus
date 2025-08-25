@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $key => $error)
                <li>{{ $key + 1 }}. {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="mb-4 border shadow-sm card card-body border-1">
            <div class="d-flex justify-content-between align-items-center">
                <div class="gap-3 d-flex align-items-center">
                    <img src="{{ $user->avatar_full_url }}" class="rounded-circle" id="uploadedAvatar" width="100" height="100" style="object-fit: cover;">
                    <div>
                        <button type="button" id="openMediaModalBtn" class="mb-2 btn btn-primary">
                            رفع صورة جديدة
                        </button>
                        <input type="text" name="avatar_url" id="avatarInput" class="d-none" accept="image/png, image/jpeg" />
                        <div class="text-muted small">مسموح JPG, GIF or PNG.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border shadow-sm card card-body border-1">
            <div class="row g-3">
                {{-- بيانات الحساب --}}
                <div class="col-md-4 col-sm-12">
                    <x-form.input label="الاسم الأول" name="first_name" :value="$user->first_name ?? ''" placeholder="محمد" required />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.input label="الاسم الأخير" name="last_name" :value="$user->last_name ?? ''" placeholder="أحمد" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.input type="email" label="البريد الإلكتروني" name="email" :value="$user->email ?? ''" placeholder="user@example.com" required />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.input label="رقم الجوال" name="phone" :value="$user->phone ?? ''" placeholder="059xxxxxxx" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.input type="date" label="تاريخ الميلاد" name="date_of_birth" :value="$user->date_of_birth ?? ''" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.selectkey label="الجنس" name="gender" :selected="$user->gender ?? ''" :options="['male' => 'ذكر', 'female' => 'أنثى']" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.selectkey label="اللغة" name="language" :selected="$user->language ?? 'ar'" :options="['ar' => 'العربية', 'en' => 'الإنجليزية']" required />
                </div>

                {{-- كلمة المرور --}}
                <div class="col-md-4 col-sm-12">
                    @if (isset($btn_label))
                        <x-form.input type="password" min="6" label="كلمة المرور" name="password" placeholder="****" />
                    @else
                        <x-form.input type="password" min="6" label="كلمة المرور" name="password" placeholder="****" required />
                    @endif
                </div>
                @if (!isset($btn_label))
                    <div class="col-md-4 col-sm-12">
                        <x-form.input type="password" label="تأكيد كلمة المرور" name="confirm_password" placeholder="****" />
                    </div>
                @endif

                {{-- إعدادات النظام --}}
                <div class="col-md-4 col-sm-12">
                    <x-form.selectkey label="الحالة" name="is_active" :selected="$user->is_active ?? 1" :options="[1 => 'نشط', 0 => 'غير نشط']" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.selectkey label="محظور؟" name="is_banned" :selected="$user->is_banned ?? 0" :options="[0 => 'لا', 1 => 'نعم']" />
                </div>

                {{-- الإشعارات --}}
                <div class="col-md-4 col-sm-12">
                    <x-form.selectkey label="إشعارات البريد" name="email_notifications" :selected="$user->email_notifications ?? 1" :options="[1 => 'مفعل', 0 => 'غير مفعل']" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.selectkey label="إشعارات الجوال" name="push_notifications" :selected="$user->push_notifications ?? 1" :options="[1 => 'مفعل', 0 => 'غير مفعل']" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <x-form.selectkey label="القفل الأبوي" name="parental_controls" :selected="$user->parental_controls ?? 0" :options="[1 => 'مفعل', 0 => 'غير مفعل']" />
                </div>

                {{-- فقط للعرض --}}
                @if (!isset($create) && isset($user->last_activity))
                    <div class="col-md-4 col-sm-12">
                        <x-form.input label="آخر ظهور" name="last_activity" :value="\Carbon\Carbon::parse($user->last_activity)->format('Y-m-d\TH:i')" disabled />
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary">
                {{ $btn_label ?? 'حفظ' }}
            </button>
        </div>
    </div>
</div>


{{-- مودال الوسائط --}}
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-6 text-2xl font-bold modal-title">📁 صور الأفاتار</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeMediaModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 modal-body">
                <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="file" name="image" id="imageInput" class="mb-2 form-control" accept="image/png, image/jpeg">
                    <button type="button" id="uploadFormBtn" class="btn btn-primary">رفع صورة</button>
                </form>
                <div id="mediaGrid" class="masonry">
                    {{-- الصور ستُملأ تلقائيًا عبر jQuery --}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
    aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذه الصورة؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="closeDeleteModal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">نعم، حذف</button>
            </div>
        </div>
    </div>
</div>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
@endpush
@push('scripts')
<script>
    const urlUpload = "{{ route('dashboard.user_avatars.store') }}";
    const urlIndex = "{{ route('dashboard.user_avatars.index') }}";
    const urlDelete = "{{ route('dashboard.user_avatars.destroy', ':id') }}";
    const urlEdit = "{{ route('dashboard.user_avatars.update', ':id') }}";
    const _token = "{{ csrf_token() }}";
    const urlAssetPath = "{{ config('app.url') }}";
</script>
<script src="{{ asset('js/custom/user_avatar_page.js') }}"></script>
@endpush
