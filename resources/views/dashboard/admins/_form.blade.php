<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <!-- Account -->
            <div class="card-body">
                <div class="gap-6 d-flex align-items-start align-items-sm-center">
                    <img src="{{ $admin->avatar_url }}" alt="admin-avatar" class="rounded-lg d-block w-px-100 h-px-100"
                        id="uploadedAvatar" style="object-fit: cover;" />
                    <div class="button-wrapper">
                        <label for="upload" class="mb-4 btn btn-primary me-3" tabindex="0">
                            <span class="d-none d-sm-block">رفع صورة جديدة</span>
                            <i class="ti ti-upload d-block d-sm-none"></i>
                            <input type="file" name="avatarUpload" id="upload" class="account-file-input" hidden
                                accept="image/png, image/jpeg" />
                        </label>
                        <div>مسموح JPG, GIF or PNG.</div>
                    </div>
                </div>
            </div>
            <!-- /Account -->
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name') }}" :value="$admin->name" name="name"
                            placeholder="{{ __('admin.Name_placeholder') }}" required autofocus />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input type="email" label="{{ __('admin.Email') }}" :value="$admin->email" name="email"
                            placeholder="{{ __('admin.Email_placeholder') }}" required />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label=" {{ __('admin.Username') }}" :value="$admin->username" name="username"
                            placeholder="{{ __('admin.Username_placeholder') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label=" {{ __('admin.tax') }}" :value="$admin->tax" name="tax"
                            placeholder="{{ __('admin.tax') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        @if (isset($btn_label))
                            <x-form.input type="password" min="6" label="{{ __('admin.Password') }}"
                                name="password" placeholder="****" />
                        @else
                            <x-form.input type="password" min="6" label="{{ __('admin.Password') }}"
                                name="password" placeholder="****" required />
                        @endif
                    </div>
                    <div class="mb-4 col-md-6">
                        @if (!isset($btn_label))
                            <x-form.input type="password" min="6" label="{{ __('admin.Confirm_password') }}"
                                name="confirm_password" placeholder="****" required />
                        @endif
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
            <div class="mb-3 border shadow card border-1" id="permissions-box"
                style="display: {{ $admin->admin_type == 'admin' || $admin->admin_type == 'vendor' || $admin->admin_type == null ? 'block' : 'none' }};">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin.Permission') }} </th>
                                        <th colspan="7">التفعيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (app('abilities') as $abilities_name => $ability_array)
                                        @php
                                            // تحقق إذا كانت جميع الصلاحيات الفرعية موجودة في صلاحيات المستخدم
                                            $adminAbilities = $admin->roles()->pluck('role_name')->toArray();
                                            $allAbilities = array_map(function ($key) use ($abilities_name) {
                                                return $abilities_name . '.' . $key;
                                            }, array_keys(
                                                array_filter(
                                                    $ability_array,
                                                    fn($key) => $key !== 'name',
                                                    ARRAY_FILTER_USE_KEY,
                                                ),
                                            ));
                                            $isAllChecked = empty(array_diff($allAbilities, $adminAbilities));
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
                                                                @checked(in_array($abilities_name . '.' . $ability_name, $admin->roles()->pluck('role_name')->toArray()))>
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
            });
        </script>
    @endpush
</div>
