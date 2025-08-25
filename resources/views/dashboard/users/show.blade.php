<x-dashboard-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-user-view.css') }}" />
    @endpush

    <div class="row">
        <!-- User Sidebar -->
        <div class="order-1 col-xl-4 col-lg-5 order-md-0">
            <div class="mb-4 card">
                <div class="pt-4 text-center card-body">
                    <div class="avatar avatar-{{ $user->last_activity >= now()->subMinutes(5) ? 'online' : 'offline' }} mx-auto"
                        style="width: 120px; height: 120px;">
                        <img class="rounded img-fluid" src="{{ $user->avatar_full_url }}" height="120" width="120"
                            alt="User avatar" />
                    </div>
                    <h5 class="mt-3">{{ $user->full_name }}</h5>
                    <ul class="mt-4 list-unstyled text-start">
                        <li class="mb-2"><strong>البريد الإلكتروني:</strong> {{ $user->email }}</li>
                        <li class="mb-2"><strong>الحالة:</strong>
                            {{ $user->last_activity >= now()->subMinutes(5) ? 'نشط' : 'غير نشط' }}</li>
                        <li class="mb-2"><strong>الدولة:</strong> {{ $user->country?->name_ar }}</li>
                    </ul>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('dashboard.users.edit', $user->id) }}" class="btn btn-primary">تعديل</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details with Tabs -->
        <div class="col-xl-8 col-lg-7 order-0 order-md-1">
            <div class="card">
                <div class="card-header border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-details"
                                role="tab">التفاصيل</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-profiles"
                                role="tab">الملفات الشخصية</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sessions"
                                role="tab">الجلسات</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notifications"
                                role="tab">الإشعارات</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <!-- التفاصيل -->
                    <div class="tab-pane fade show active" id="tab-details">
                        <div class="row">
                            <div class="mb-3 col-md-6"><strong>الاسم الأول:</strong> {{ $user->first_name }}</div>
                            <div class="mb-3 col-md-6"><strong>الاسم الأخير:</strong> {{ $user->last_name }}</div>
                            <div class="mb-3 col-md-6"><strong>رقم الجوال:</strong> {{ $user->phone }}</div>
                            <div class="mb-3 col-md-6"><strong>الجنس:</strong>
                                {{ $user->gender == 'male' ? 'ذكر' : 'أنثى' }}</div>
                            <div class="mb-3 col-md-6"><strong>تاريخ الميلاد:</strong> {{ $user->date_of_birth }}</div>
                            <div class="mb-3 col-md-6"><strong>اللغة:</strong> {{ $user->language }}</div>
                        </div>
                    </div>

                    <!-- الملفات الشخصية -->
                    <div class="tab-pane fade" id="tab-profiles">
                        @if ($user->profiles->count())
                            <ul class="list-group">
                                @foreach ($user->profiles as $profile)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $profile->name }} - {{ $profile->language }}
                                        <span
                                            class="badge bg-secondary">{{ $profile->is_child_profile ? 'ملف طفل' : 'عادي' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">لا يوجد ملفات شخصية</p>
                        @endif
                    </div>

                    <!-- الجلسات -->
                    <div class="tab-pane fade" id="tab-sessions">
                        @if ($user->sessions->count())
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>الجهاز</th>
                                            <th>المنصة</th>
                                            <th>IP</th>
                                            <th>آخر نشاط</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->sessions as $session)
                                            <tr>
                                                <td>{{ $session->device_name }}</td>
                                                <td>{{ $session->platform }}</td>
                                                <td>{{ $session->ip_address }}</td>
                                                <td>{{ $session->last_activity }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">لا يوجد جلسات نشطة</p>
                        @endif
                    </div>

                    <!-- الإشعارات -->
                    <div class="tab-pane fade" id="tab-notifications">
                        @if ($user->notifications->count())
                            <ul class="list-group">
                                @foreach ($user->notifications as $notification)
                                    <li class="list-group-item">
                                        {{ $notification->data['title'] ?? 'بدون عنوان' }}
                                        <span
                                            class="text-muted float-end">{{ $notification->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">لا توجد إشعارات</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
