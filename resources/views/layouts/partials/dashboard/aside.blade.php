<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style="overflow: visible">
        <a href="{{ route('dashboard.home') }}" class="app-brand-link">
            <span class="app-brand-logo demo" style="overflow: visible">
                <img src=" {{ asset('imgs/logo-brand.png') }}" alt="Logo" width="60">
            </span>
            {{-- <span class="app-brand-text demo menu-text fw-bold">{{ $title }}</span> --}}
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="align-middle ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="align-middle ti ti-x d-block d-xl-none ti-md"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="py-1 menu-inner">
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps &amp; Pages">العامة</span>
        </li>
        <!-- Page -->
        <li
            class="menu-item {{ request()->is('dashboard/home') || request()->is('dashboard/home/*') ? 'active' : '' }}">
            <a href="{{ route('dashboard.home') }}" class="menu-link">
                <i class="ph ph-house me-2"></i>
                <div data-i18n="home">الرئيسية</div>
            </a>
        </li>
        @can('view', 'App\\Models\Media')
            <li
                class="menu-item {{ request()->is('dashboard/media') || request()->is('dashboard/media/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.media.index') }}" class="menu-link">
                    <i class="ph ph-images me-2"></i>
                    <div data-i18n="media">{{ __('admin.Media') }}</div>
                </a>
            </li>
        @endcan
        @can('view', 'App\\Models\Notification')
            <li
                class="menu-item {{ request()->is('dashboard/notifications') || request()->is('dashboard/notifications/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.notifications.index') }}" class="menu-link">
                    <i class="ph ph-bell me-2"></i>
                    <div data-i18n="notifications">{{ __('admin.Notification') }}</div>
                </a>
            </li>
        @endcan
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps &amp; Pages">{{ __('admin.Media') }}</span>
        </li>
        @can('view', 'App\\Models\Series')
            <li
                class="menu-item {{ request()->is('dashboard/series') || request()->is('dashboard/series/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.series.index') }}" class="menu-link">
                    <i class="ph ph-monitor-play me-2"></i>
                    <div data-i18n="series">المسلسلات</div>
                </a>
            </li>
        @endcan
        @can('view', 'App\\Models\Movie')
            <li
                class="menu-item {{ request()->is('dashboard/movies') || request()->is('dashboard/movies/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.movies.index') }}" class="menu-link">
                    <i class="ph ph-film-strip me-2"></i>
                    <div data-i18n="movies">{{ __('admin.Movie') }}</div>
                </a>
            </li>
        @endcan
        @can('view', 'App\\Models\Category')
            <li
                class="menu-item {{ request()->is('dashboard/movie-categories') || request()->is('dashboard/movie-categories/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.movie-categories.index') }}" class="menu-link">
                    <i class="ph ph-film-strip me-2"></i>
                    <div data-i18n="movie-categories"> {{ __('admin.Movie Category') }} </div>
                </a>
            </li>
        @endcan

        @can('view', 'App\\Models\Short')
            <li
                class="menu-item {{ request()->is('dashboard/shorts') || request()->is('dashboard/shorts/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.shorts.index') }}" class="menu-link">
                    <i class="ph ph-user me-2"></i>
                    <div data-i18n="shorts"> {{ __('admin.Short') }}</div>
                </a>
            </li>
        @endcan

        @can('view', 'App\\Models\Person')
            <li
                class="menu-item {{ request()->is('dashboard/people') || request()->is('dashboard/people/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.people.index') }}" class="menu-link">
                    <i class="ph ph-user me-2"></i>
                    <div data-i18n="people"> {{ __('admin.Person') }}</div>
                </a>
            </li>
        @endcan

        @can('view', 'App\\Models\UserRating')
            <li
                class="menu-item {{ request()->is('dashboard/userRatings') || request()->is('dashboard/userRatings/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.userRatings.index') }}" class="menu-link">
                    <i class="ph ph-user me-2"></i>
                    <div data-i18n="userRatings"> {{ __('admin.userRatings') }}</div>
                </a>
            </li>
        @endcan

        @can('view', 'App\\Models\Download')
            <li
                class="menu-item {{ request()->is('dashboard/downloads') || request()->is('dashboard/userRatings/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.downloads.index') }}" class="menu-link">
                    <i class="ph ph-user me-2"></i>
                    <div data-i18n="downloads"> {{ __('admin.downloads') }}</div>
                </a>
            </li>
        @endcan


        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps &amp; Pages">الإعدادات</span>
        </li>
        @can('view', 'App\\Models\Admin')
            <li
                class="menu-item {{ request()->is('dashboard/admins') || request()->is('dashboard/admins/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.admins.index') }}" class="menu-link">
                    <i class="ph ph-users-three me-2"></i>
                    <div data-i18n="admins">{{ __('admin.Admin') }}</div>
                </a>
            </li>
        @endcan
        @can('view', 'App\\Models\User')
            <li
                class="menu-item {{ request()->is('dashboard/users') || request()->is('dashboard/users/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.users.index') }}" class="menu-link">
                    <i class="ph ph-users me-2"></i>
                    <div data-i18n="users">{{ __('admin.User') }}</div>
                </a>
            </li>
        @endcan
        @can('view', 'App\\Models\UserAvatar')
            <li
                class="menu-item {{ request()->is('dashboard/user_avatars') || request()->is('dashboard/user_avatars/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.user_avatars.index') }}" class="menu-link">
                    <i class="ph ph-user-circle me-2"></i>
                    <div data-i18n="user-avatar">{{ __('admin.UserAvatar') }} </div>
                </a>
            </li>
        @endcan
        @can('view', 'App\\Models\Country')
            <li
                class="menu-item {{ request()->is('dashboard/countries') || request()->is('dashboard/countries/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.countries.index') }}" class="menu-link">
                    <i class="ph ph-globe me-2"></i>
                    <div data-i18n="users">{{ __('admin.Country') }}</div>
                </a>
            </li>
        @endcan

        @can('view', 'App\\Models\SystemSetting')
            <li
                class="menu-item {{ request()->is('dashboard/settings') || request()->is('dashboard/settings/*') ? 'active' : '' }}">
                <a href="{{ route('dashboard.settings.edit') }}" class="menu-link">
                    <i class="ph ph-gear me-2"></i>
                    <div data-i18n="users">{{ __('admin.SystemSetting') }}</div>
                </a>
            </li>
        @endcan

        {{-- <li class="menu-item">
            <a href="page-2.html" class="menu-link">
                <i class="menu-icon tf-icons ti ti-app-window"></i>
                <i class="fa-solid fa-house me-2"></i>
                <div data-i18n="Page 2"> {{ __('admin.Page') }}</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">{{ __('admin.Dashboards') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="index.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-chart-pie-2"></i>
                        <div data-i18n="Analytics">{{ __('admin.Analytics') }}</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="dashboards-crm.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-3d-cube-sphere"></i>
                        <div data-i18n="CRM">CRM</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-ecommerce-dashboard.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                        <div data-i18n="eCommerce">eCommerce</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-logistics-dashboard.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-truck"></i>
                        <div data-i18n="Logistics">Logistics</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-academy-dashboard.html" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-book"></i>
                        <div data-i18n="Academy">Academy</div>
                    </a>
                </li>
            </ul>
        </li> --}}
    </ul>
    {{-- <div class="my-3 text-center text-white text-body">
        ©
        2025
        , تم الإنشاء ❤️ بواسطة <a href="https://saeyd-jamal.github.io/" target="_blank" class="footer-link">م
            . السيد الاخرسي</a>
    </div> --}}
</aside>
