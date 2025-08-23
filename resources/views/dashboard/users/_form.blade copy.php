<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <!-- Account -->
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="gap-6 d-flex align-items-start align-items-sm-center">
                        <img src="{{ $user->avatar_url }}" alt="user-avatar" class="rounded d-block w-px-100 h-px-100"
                            id="uploadedAvatar" style="object-fit: cover;" />
                        <div class="button-wrapper">
                            <label for="upload" class="mb-4 btn btn-primary me-3" tabindex="0">
                                <span class="d-none d-sm-block">رفع صورة جديدة</span>
                                <i class="ti ti-upload d-block d-sm-none"></i>
                                <input type="file" name="avatarUpload" id="upload" class="account-file-input"
                                    hidden accept="image/png, image/jpeg" />
                            </label>
                            <div>مسموح JPG, GIF or PNG.</div>
                        </div>
                    </div>
                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey required label="نوع المستخدم" id="user-type" name="user_type" :selected="$user->user_type ?? 'vendor'"
                            :options="['admin' => 'مشرف', 'user' => 'مستخدم', 'vendor' => 'مزود']" />
                    </div>
                </div>
            </div>
            <!-- /Account -->
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- حقول عامة --}}
                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="الاسم الأول" name="first_name" :value="$user->first_name" placeholder="محمد"
                            required />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="الاسم الأخير" name="last_name" :value="$user->last_name" placeholder="أحمد" />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="اسم المستخدم" name="username" :value="$user->username" placeholder="username" />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input type="email" label="البريد الإلكتروني" name="email" :value="$user->email"
                            placeholder="example@gmail.com" required />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        @if (isset($btn_label))
                            <x-form.input type="password" label="كلمة المرور" name="password" placeholder="****" />
                        @else
                            <x-form.input type="password" label="كلمة المرور" name="password" placeholder="****"
                                required />
                        @endif
                    </div>

                    @if (!isset($btn_label))
                        <div class="mb-4 col-md-3 col-sm-6">
                            <x-form.input type="password" label="تأكيد كلمة المرور" name="confirm_password"
                                placeholder="****" required />
                        </div>
                    @endif

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="رقم الجوال" name="mobile" :value="$user->mobile" placeholder="059xxxxxxx" />
                    </div>

                    {{-- <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey label="نوع الدخول" name="login_type" :selected="$user->login_type" :options="['manual' => 'يدوي', 'google' => 'جوجل', 'facebook' => 'فيسبوك']" />
                    </div> --}}

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey label="الجنس" name="gender" :selected="$user->gender" :options="['male' => 'ذكر', 'female' => 'أنثى']" />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input type="date" label="تاريخ الميلاد" name="date_of_birth" :value="$user->date_of_birth" />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey label="الحالة" name="status" :selected="$user->status ?? 1" :options="[1 => 'نشط', 0 => 'غير نشط']" />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="العنوان" name="address" :value="$user->address"
                            placeholder="غزة - الرمال ..." />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input type="datetime-local" label="آخر ظهور" name="last_activity"
                            :value="\Carbon\Carbon::parse($user->last_activity)->format('Y-m-d\TH:i')" disabled />
                    </div>
                </div>
                {{-- حقول خاصة بالمستخدمين --}}
                <div id="user-box" class="row" style="display: {{$user->user_type == 'user' ? 'block' : 'none' }};">
                    <hr>
                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="PIN" name="pin" :value="$user->pin" placeholder="مثلاً: 1234"
                            readonly />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="OTP" name="otp" :value="$user->otp" placeholder="رمز التحقق المؤقت"
                            readonly />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey label="القفل الأبوي" name="is_parental_lock_enable" :selected="$user->is_parental_lock_enable"
                            :options="[1 => 'مفعل', 0 => 'غير مفعل']" disabled />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey label="هل محظور؟" name="is_banned" :selected="$user->is_banned" :options="[1 => 'محظور', 0 => 'غير محظور']"
                            disabled />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey label="مشترك؟" name="is_subscribe" :selected="$user->is_subscribe" :options="[1 => 'مشترك', 0 => 'غير مشترك']"
                            disabled />
                    </div>

                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.input label="كود الأب (Father Code)" name="father_code" :value="$user->father_code" readonly />
                    </div>
                </div>
                {{-- حقول خاصة بالمسؤولين --}}
                <div id="admin-box" class="row" style="display: {{$user->user_type == 'admin' ? 'block' : 'none' }};">
                    <hr>
                    <div class="mb-4 col-md-3 col-sm-6">
                        <x-form.selectkey label="مشرف عام؟" name="super_admin" :selected="$user->super_admin"
                            :options="[1 => 'نعم', 0 => 'لا']" />
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
        @if (!isset($settings_profile))
            <div class="mb-3 border shadow card border-1" id="permissions-box" style="display: {{$user->user_type == 'admin' || $user->user_type == 'vendor' || $user->user_type == null ? 'block' : 'none' }};">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>صلاحيات المستخدم</th>
                                        <th colspan="7">التفعيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (app('abilities') as $abilities_name => $ability_array)
                                        @php
                                            // تحقق إذا كانت جميع الصلاحيات الفرعية موجودة في صلاحيات المستخدم
                                            $userAbilities = $user->roles()->pluck('role_name')->toArray();
                                            $allAbilities = array_map(function ($key) use ($abilities_name) {
                                                return $abilities_name . '.' . $key;
                                            }, array_keys(
                                                array_filter(
                                                    $ability_array,
                                                    fn($key) => $key !== 'name',
                                                    ARRAY_FILTER_USE_KEY,
                                                ),
                                            ));
                                            $isAllChecked = empty(array_diff($allAbilities, $userAbilities));
                                        @endphp
                                        <tr>
                                            <td class="table-light">
                                                <!-- Checkbox رئيسي لتحديد الكل -->
                                                <input class="form-check-input master-checkbox" type="checkbox"
                                                    id="master-{{ $abilities_name }}"
                                                    data-target="ability-group-{{ $abilities_name }}"
                                                    @checked($isAllChecked)>
                                                <label for="master-{{ $abilities_name }}">
                                                    {{ $ability_array['name'] }}
                                                </label>
                                            </td>
                                            @foreach ($ability_array as $ability_name => $ability)
                                                @if ($ability_name != 'name')
                                                    <td>
                                                        <div class="custom-control custom-checkbox"
                                                            style="margin-right: 0;">
                                                            <input
                                                                class="form-check-input ability-group-{{ $abilities_name }}"
                                                                type="checkbox" name="abilities[]"
                                                                id="ability-{{ $abilities_name . '.' . $ability_name }}"
                                                                value="{{ $abilities_name . '.' . $ability_name }}"
                                                                @checked(in_array($abilities_name . '.' . $ability_name, $user->roles()->pluck('role_name')->toArray()))>
                                                            <label class="form-check-label"
                                                                for="ability-{{ $abilities_name . '.' . $ability_name }}">
                                                                {{ $ability }}
                                                            </label>
                                                        </div>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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

                // عند تغيير نوع المستخدم
                $('#user-type').on('change', function() {
                    const userType = $(this).val();
                    $('#user-box').toggle(userType === 'user');
                    $('#admin-box').toggle(userType === 'admin');
                    $('#permissions-box').toggle(userType === 'admin' || userType === 'vendor');
                });

                $('#super_admin').on('change', function() {
                    const superAdmin = $(this).val();
                    $('#permissions-box').toggle(superAdmin == 1);
                });

            });
        </script>
    @endpush
</div>
