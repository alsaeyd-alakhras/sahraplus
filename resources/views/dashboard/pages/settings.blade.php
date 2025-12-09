<x-dashboard-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
    <style>
        .settings-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        .settings-card .card-header {
            background: linear-gradient(135deg, var(--bs-primary, #6f42c1) 0%, var(--bs-primary, #6f42c1) 100%);
            border: none;
            color: white;
            font-weight: 600;
        }
        h5,h6{
            color: white;
        }
        .preview-image {
            border: 2px solid var(--bs-border-color, #dee2e6);
            border-radius: 0.375rem;
            padding: 0.75rem;
            background-color: var(--bs-gray-50, #f8f9fa);
        }
        .form-section {
            margin-bottom: 2rem;
        }
        .input-group-text {
            background-color: var(--bs-light, #f8f9fa);
            border-color: var(--bs-border-color, #dee2e6);
        }
        .social-preview {
            margin-top: 1rem;
            padding: 1rem;
            background-color: var(--bs-light, #f8f9fa);
            border-radius: 0.375rem;
            border: 1px solid var(--bs-border-color, #dee2e6);
        }
        .social-preview a {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            margin: 0.25rem;
            text-decoration: none;
            background: white;
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: 0.375rem;
            color: var(--bs-body-color, #212529);
            font-size: 0.875rem;
        }
        .social-preview a:hover {
            background-color: var(--bs-primary, #6f42c1);
            color: white;
            border-color: var(--bs-primary, #6f42c1);
        }
        .maintenance-alert {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #f6d55c;
            color: #856404;
        }
        .color-picker-wrapper {
            position: relative;
        }
        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 0.375rem;
            border: 2px solid var(--bs-border-color, #dee2e6);
            display: inline-block;
            margin-left: 0.5rem;
        }
        .nav-pills .nav-link.active {
            background-color: var(--bs-primary, #6f42c1) !important;
        }
        .nav-pills .nav-link {
            color: var(--bs-body-color, #212529);
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
        }
        .nav-pills .nav-link:hover {
            background-color: var(--bs-light, #f8f9fa);
        }
    </style>
    @endpush

    <div class="row">
        <!-- الشريط الجانبي للتبويبات -->
        <div class="col-lg-3 col-md-4">
            <div class="card settings-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>{{ __('admin.settings_section') }}
                    </h6>
                </div>
                <div class="px-2 py-2 card-body">
                    <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#general-tab" type="button" role="tab">
                            <i class="fas fa-info-circle me-2"></i>{{ __('admin.general_info') }}
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#branding-tab" type="button" role="tab">
                            <i class="fas fa-palette me-2"></i>{{ __('admin.visual_identity') }}
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#streaming-tab" type="button" role="tab">
                            <i class="fas fa-play me-2"></i>{{ __('admin.content_settings') }}
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#seo-tab" type="button" role="tab">
                            <i class="fas fa-search me-2"></i>{{ __('admin.seo_settings') }}
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#system-tab" type="button" role="tab">
                            <i class="fas fa-server me-2"></i>{{ __('admin.system_settings') }}
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#mobile-tab" type="button" role="tab">
                            <i class="fas fa-mobile-alt me-2"></i>{{ __('admin.mobile_app_settings') }}
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#social-tab" type="button" role="tab">
                            <i class="fas fa-share-alt me-2"></i>{{ __('admin.social_media') }}
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#maintenance-tab" type="button" role="tab">
                            <i class="fas fa-tools me-2"></i>{{ __('admin.maintenance_mode') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- محتوى الإعدادات -->
        <div class="col-lg-9 col-md-8">
            <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-0 tab-content" id="settings-content">
                    <!-- المعلومات العامة -->
                    <div class="tab-pane fade show active" id="general-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="mb-3 card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>{{ __('admin.general_info') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.site_name_ar') }}"
                                            name="site_name_ar"
                                            :value="$setting['site_name_ar'] ?? ''"
                                            required
                                            placeholder="{{ __('admin.site_name_ar_placeholder') }}" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.site_name_en') }}"
                                            name="site_name_en"
                                            class="text-left"
                                            :value="$setting['site_name_en'] ?? ''"
                                            required
                                            placeholder="{{ __('admin.site_name_en_placeholder') }}" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.textarea name="site_description_ar" rows="2" label="{{ __('admin.site_description_ar') }}"
                                            placeholder="{{ __('admin.site_description_ar_placeholder') }}" :value="$setting['site_description_ar'] ?? ''" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.textarea name="site_description_en" rows="2" label="{{ __('admin.site_description_en') }}" class="text-left"
                                            placeholder="{{ __('admin.site_description_en_placeholder') }}" :value="$setting['site_description_en'] ?? ''" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.site_email') }}"
                                            name="site_email"
                                            type="email"
                                            :value="$setting['site_email'] ?? ''"
                                            class="text-left"
                                            placeholder="contact@sahra.com" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.site_phone') }}"
                                            name="site_phone"
                                            :value="$setting['site_phone'] ?? ''"
                                            placeholder="966501234567+" />
                                    </div>
                                       <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.taxes') }}"
                                            name="site_tax"
                                            :value="$setting['site_tax'] ?? ''"
                                           />
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <x-form.input
                                            label="{{ __('admin.site_address') }}"
                                            name="site_address"
                                            :value="$setting['site_address'] ?? ''"
                                            placeholder="{{ __('admin.site_address_placeholder') }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الهوية البصرية -->
                    <div class="tab-pane fade" id="branding-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="mb-3 card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-palette me-2"></i>{{ __('admin.visual_identity') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- الشعار -->
                                    <div class="mb-4 col-md-6">
                                        <div class="p-3 rounded border">
                                            <h6 class="mb-3 text-muted">
                                                <i class="fas fa-image me-2"></i>{{ __('admin.main_logo') }}
                                            </h6>
                                            <x-form.input
                                                label="{{ __('admin.logo_url') }}"
                                                name="logo_url"
                                                :value="$setting['logo_url'] ?? ''"
                                                placeholder="https://example.com/logo.png" />
                                            <div class="mt-3">
                                                <label class="form-label">{{ __('admin.upload_new_logo') }}</label>
                                                <input type="file" name="logoUpload" class="form-control" accept="image/*" />
                                                <small class="text-muted">{{ __('admin.max_size_2mb') }}</small>
                                            </div>
                                            @if(!empty($setting['logo_url']))
                                            <div class="mt-3 text-center preview-image">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting['logo_url']) }}"
                                                    alt="logo" style="max-height:80px;" class="img-fluid">
                                                <p class="mt-2 mb-0 text-muted small">{{ __('admin.current_logo') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- الأيقونة -->
                                    <div class="mb-4 col-md-6">
                                        <div class="p-3 rounded border">
                                            <h6 class="mb-3 text-muted">
                                                <i class="fas fa-bookmark me-2"></i>{{ __('admin.browser_icon') }}
                                            </h6>
                                            <x-form.input
                                                label="{{ __('admin.favicon_url') }}"
                                                name="favicon_url"
                                                :value="$setting['favicon_url'] ?? ''"
                                                placeholder="https://example.com/favicon.ico" />
                                            <div class="mt-3">
                                                <label class="form-label">{{ __('admin.upload_new_favicon') }}</label>
                                                <input type="file" name="faviconUpload" class="form-control" accept="image/*" />
                                                <small class="text-muted">{{ __('admin.suggested_size_32') }}</small>
                                            </div>
                                            @if(!empty($setting['favicon_url']))
                                            <div class="mt-3 text-center preview-image">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting['favicon_url']) }}"
                                                    alt="favicon" style="max-height:32px;">
                                                <p class="mt-2 mb-0 text-muted small">{{ __('admin.current_favicon') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- ألوان المنصة -->
                                    <div class="col-md-12">
                                        <div class="p-3 rounded border">
                                            <h6 class="mb-3 text-muted">
                                                <i class="fas fa-palette me-2"></i>{{ __('admin.site_colors') }}
                                            </h6>
                                            <div class="row">
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">{{ __('admin.primary_color') }}</label>
                                                    <div class="d-flex align-items-center">
                                                        <input type="color" class="form-control form-control-color"
                                                            name="primary_color" value="{{ $setting['primary_color'] ?? '#6f42c1' }}" style="width: 60px;">
                                                        <span class="ms-2 small text-muted">{{ $setting['primary_color'] ?? '#6f42c1' }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">{{ __('admin.secondary_color') }}</label>
                                                    <div class="d-flex align-items-center">
                                                        <input type="color" class="form-control form-control-color"
                                                            name="secondary_color" value="{{ $setting['secondary_color'] ?? '#6c757d' }}" style="width: 60px;">
                                                        <span class="ms-2 small text-muted">{{ $setting['secondary_color'] ?? '#6c757d' }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">{{ __('admin.background_color') }}</label>
                                                    <div class="d-flex align-items-center">
                                                        <input type="color" class="form-control form-control-color"
                                                            name="background_color" value="{{ $setting['background_color'] ?? '#ffffff' }}" style="width: 60px;">
                                                        <span class="ms-2 small text-muted">{{ $setting['background_color'] ?? '#ffffff' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إعدادات المحتوى -->
                    <div class="tab-pane fade" id="streaming-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-play me-2"></i>{{ __('admin.content_and_streaming') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('admin.default_quality') }}</label>
                                        <select class="form-select" name="default_quality">
                                            <option value="auto" {{ ($setting['default_quality'] ?? '') == 'auto' ? 'selected' : '' }}>{{ __('admin.quality_auto') }}</option>
                                            <option value="480" {{ ($setting['default_quality'] ?? '') == '480' ? 'selected' : '' }}>{{ __('admin.quality_480p') }}</option>
                                            <option value="720" {{ ($setting['default_quality'] ?? '') == '720' ? 'selected' : '' }}>{{ __('admin.quality_720p') }}</option>
                                            <option value="1080" {{ ($setting['default_quality'] ?? '') == '1080' ? 'selected' : '' }}>{{ __('admin.quality_1080p') }}</option>
                                            <option value="4k" {{ ($setting['default_quality'] ?? '') == '4k' ? 'selected' : '' }}>{{ __('admin.quality_4k') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('admin.items_per_page') }}</label>
                                        <input type="number" class="form-control" name="items_per_page"
                                            value="{{ $setting['items_per_page'] ?? '20' }}" min="10" max="100">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="auto_play" value="1"
                                                {{ ($setting['auto_play'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.auto_play_trailers') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="enable_download" value="1"
                                                {{ ($setting['enable_download'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.enable_download') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="enable_comments" value="1"
                                                {{ ($setting['enable_comments'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.enable_comments') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="enable_ratings" value="1"
                                                {{ ($setting['enable_ratings'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.enable_ratings') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">{{ __('admin.copyright_notice') }}</label>
                                        <textarea class="form-control" name="copyright_notice" rows="3"
                                            placeholder="{{ __('admin.copyright_notice_placeholder') }}">{{ $setting['copyright_notice'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- محرك البحث -->
                    <div class="tab-pane fade" id="seo-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-search me-2"></i>{{ __('admin.seo_settings') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <x-form.input
                                            label="{{ __('admin.meta_title') }}"
                                            name="meta_title"
                                            :value="$setting['meta_title'] ?? ''"
                                            placeholder="{{ __('admin.meta_title_placeholder') }}" />
                                        <small class="text-muted">{{ __('admin.max_60_chars') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <x-form.textarea name="meta_description" rows="3" label="{{ __('admin.meta_description') }}"
                                            placeholder="{{ __('admin.meta_description_placeholder') }}" :value="$setting['meta_description'] ?? ''" />
                                        <small class="text-muted">{{ __('admin.max_160_chars') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <x-form.input label="{{ __('admin.meta_keywords') }}" name="meta_keywords" :value="$setting['meta_keywords'] ?? ''" placeholder="{{ __('admin.meta_keywords_placeholder') }}" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.google_analytics_id') }}"
                                            name="google_analytics_id"
                                            :value="$setting['google_analytics_id'] ?? ''"
                                            placeholder="G-XXXXXXXXXX" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.google_search_console') }}"
                                            name="google_search_console"
                                            :value="$setting['google_search_console'] ?? ''"
                                            placeholder="meta tag verification code" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إعدادات النظام -->
                    <div class="tab-pane fade" id="system-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-server me-2"></i>{{ __('admin.system_settings') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('admin.default_country') }}</label>
                                        <select class="form-select" name="default_country">
                                            <option value="SA" {{ ($setting['default_country'] ?? '') == 'SA' ? 'selected' : '' }}>{{ __('admin.saudi_arabia') }}</option>
                                            <option value="EG" {{ ($setting['default_country'] ?? '') == 'EG' ? 'selected' : '' }}>{{ __('admin,egypt') }}</option>
                                            <option value="AE" {{ ($setting['default_country'] ?? '') == 'AE' ? 'selected' : '' }}>{{ __('admin.uae') }}</option>
                                            <option value="JO" {{ ($setting['default_country'] ?? '') == 'JO' ? 'selected' : '' }}>{{ __('admin.jordan') }}</option>
                                            <option value="KW" {{ ($setting['default_country'] ?? '') == 'KW' ? 'selected' : '' }}>{{ __('admin.kuwait') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('admin.default_currency') }}</label>
                                        <select class="form-select" name="default_currency">
                                            <option value="SAR" {{ ($setting['default_currency'] ?? '') == 'SAR' ? 'selected' : '' }}>{{ __('admin.saudi Riyal') }} (SAR)</option>
                                            <option value="EGP" {{ ($setting['default_currency'] ?? '') == 'EGP' ? 'selected' : '' }}>{{ __('admin,egyptian pound') }} (EGP)</option>
                                            <option value="AED" {{ ($setting['default_currency'] ?? '') == 'AED' ? 'selected' : '' }}>{{ __('admin.emirati dirham') }} (AED)</option>
                                            <option value="USD" {{ ($setting['default_currency'] ?? '') == 'USD' ? 'selected' : '' }}>{{ __('admin.american dollar') }} (USD)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">{{ __('admin.timezone') }}</label>
                                        <select class="form-select" name="timezone">
                                            <option value="Asia/Riyadh" {{ ($setting['timezone'] ?? '') == 'Asia/Riyadh' ? 'selected' : '' }}>{{ __('admin.riyadh') }}</option>
                                            <option value="Asia/Dubai" {{ ($setting['timezone'] ?? '') == 'Asia/Dubai' ? 'selected' : '' }}>{{ __('admin.dubai') }}</option>
                                            <option value="Africa/Cairo" {{ ($setting['timezone'] ?? '') == 'Africa/Cairo' ? 'selected' : '' }}>{{ __('admin.cairo') }}</option>
                                            <option value="Asia/Amman" {{ ($setting['timezone'] ?? '') == 'Asia/Amman' ? 'selected' : '' }}>{{ __('admin.amman') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('admin.default_language') }}</label>
                                        <select class="form-select" name="default_language">
                                            <option value="ar" {{ ($setting['default_language'] ?? '') == 'ar' ? 'selected' : '' }}>{{ __('admin.arabic') }}</option>
                                            <option value="en" {{ ($setting['default_language'] ?? '') == 'en' ? 'selected' : '' }}>{{ __('admin.english') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('admin.number_of_login_attempts') }}</label>
                                        <input type="number" class="form-control" name="max_login_attempts"
                                            value="{{ $setting['max_login_attempts'] ?? '5' }}" min="3" max="10">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="user_registration" value="1"
                                                {{ ($setting['user_registration'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.allow_user_registration') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="email_verification" value="1"
                                                {{ ($setting['email_verification'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.email_verification_required') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إعدادات تطبيق الهاتف -->
                    <div class="tab-pane fade" id="mobile-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-mobile-alt me-2"></i>{{ __('admin.mobile_app_settings') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.android_app_url') }}"
                                            name="android_app_url"
                                            :value="$setting['android_app_url'] ?? ''"
                                            placeholder="https://play.google.com/store/apps/details?id=com.example.app" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.ios_app_url') }}"
                                            name="ios_app_url"
                                            :value="$setting['ios_app_url'] ?? ''"
                                            placeholder="https://apps.apple.com/app/id0000000000" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <x-form.input
                                            label="{{ __('admin.huawei_app_url') }}"
                                            name="huawei_app_url"
                                            :value="$setting['huawei_app_url'] ?? ''"
                                            placeholder="https://appgallery.huawei.com/#/app/C000000" />
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <x-form.input
                                            label="{{ __('admin.app_version') }}"
                                            name="app_version"
                                            :value="$setting['app_version'] ?? '1.0.0'"
                                            placeholder="1.0.0" />
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <x-form.input
                                            label="{{ __('admin.min_supported_version') }}"
                                            name="min_supported_version"
                                            :value="$setting['min_supported_version'] ?? '1.0.0'"
                                            placeholder="1.0.0" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="force_update" value="1"
                                                {{ ($setting['force_update'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.force_update') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">{{ __('admin.mobile_update_message') }}</label>
                                        <textarea class="form-control" name="mobile_update_message" rows="3"
                                            placeholder="{{ __('admin.mobile_update_message_placeholder') }}">{{ $setting['mobile_update_message'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التواصل الاجتماعي -->
                    <div class="tab-pane fade" id="social-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-share-alt me-2"></i>{{ __('admin.social_media_links') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-facebook text-primary"></i>
                                            </span>
                                            <input type="url" class="form-control" name="facebook_url"
                                                placeholder="{{ __('admin.facebook_placeholder') }}"
                                                value="{{ $setting['facebook_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.facebook_page') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-twitter text-info"></i>
                                            </span>
                                            <input type="url" class="form-control" name="twitter_url"
                                                placeholder="{{ __('admin.twitter_placeholder') }}"
                                                value="{{ $setting['twitter_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.twitter_account') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-instagram text-danger"></i>
                                            </span>
                                            <input type="url" class="form-control" name="instagram_url"
                                                placeholder="{{ __('admin.instagram_placeholder') }}"
                                                value="{{ $setting['instagram_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.instagram_account') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-youtube text-danger"></i>
                                            </span>
                                            <input type="url" class="form-control" name="youtube_url"
                                                placeholder="{{ __('admin.youtube_placeholder') }}"
                                                value="{{ $setting['youtube_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.youtube_channel') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-tiktok text-dark"></i>
                                            </span>
                                            <input type="url" class="form-control" name="tiktok_url"
                                                placeholder="{{ __('admin.tiktok_placeholder') }}"
                                                value="{{ $setting['tiktok_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.tiktok_account') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-telegram text-primary"></i>
                                            </span>
                                            <input type="url" class="form-control" name="telegram_url"
                                                placeholder="{{ __('admin.telegram_placeholder') }}"
                                                value="{{ $setting['telegram_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.telegram_channel') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-whatsapp text-success"></i>
                                            </span>
                                            <input type="url" class="form-control" name="whatsapp_url"
                                                placeholder="{{ __('admin.whatsapp_placeholder') }}"
                                                value="{{ $setting['whatsapp_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.whatsapp_number') }}</small>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fab fa-snapchat-ghost" style="color: #FFFC00;"></i>
                                            </span>
                                            <input type="url" class="form-control" name="snapchat_url"
                                                placeholder="{{ __('admin.snapchat_placeholder') }}"
                                                value="{{ $setting['snapchat_url'] ?? '' }}">
                                        </div>
                                        <small class="text-muted">{{ __('admin.snapchat_account') }}</small>
                                    </div>
                                </div>

                                <!-- معاينة الروابط -->
                                <div class="mt-4 social-preview">
                                    <h6 class="mb-3">
                                        <i class="fas fa-eye me-2"></i>{{ __('admin.active_links_preview') }}:
                                    </h6>
                                    <div class="flex-wrap gap-2 d-flex">
                                        @if(!empty($setting['facebook_url']))
                                            <a href="{{ $setting['facebook_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-facebook me-1"></i>{{ __('admin.facebook_page') }}
                                            </a>
                                        @endif
                                        @if(!empty($setting['twitter_url']))
                                            <a href="{{ $setting['twitter_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-twitter me-1"></i>{{ __('admin.twitter_account') }}
                                            </a>
                                        @endif
                                        @if(!empty($setting['instagram_url']))
                                            <a href="{{ $setting['instagram_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-instagram me-1"></i>{{ __('admin.instagram_account') }}
                                            </a>
                                        @endif
                                        @if(!empty($setting['youtube_url']))
                                            <a href="{{ $setting['youtube_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-youtube me-1"></i>{{ __('admin.youtube_channel') }}
                                            </a>
                                        @endif
                                        @if(!empty($setting['tiktok_url']))
                                            <a href="{{ $setting['tiktok_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-tiktok me-1"></i>{{ __('admin.tiktok_account') }}
                                            </a>
                                        @endif
                                        @if(!empty($setting['telegram_url']))
                                            <a href="{{ $setting['telegram_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-telegram me-1"></i>{{ __('admin.telegram_channel') }}
                                            </a>
                                        @endif
                                        @if(!empty($setting['whatsapp_url']))
                                            <a href="{{ $setting['whatsapp_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-whatsapp me-1"></i>{{ __('admin.whatsapp_channel') }}
                                            </a>
                                        @endif
                                        @if(!empty($setting['snapchat_url']))
                                            <a href="{{ $setting['snapchat_url'] }}" target="_blank" class="social-link">
                                                <i class="fab fa-snapchat-ghost me-1"></i>{{ __('admin.snapchat_account') }}
                                            </a>
                                        @endif
                                    </div>
                                    @if(empty($setting['facebook_url']) && empty($setting['twitter_url']) && empty($setting['instagram_url']))
                                        <p class="mb-0 text-muted">{{ __('admin.no_active_links') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- وضع الصيانة -->
                    <div class="tab-pane fade" id="maintenance-tab" role="tabpanel">
                        <div class="card settings-card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-tools me-2"></i>{{ __('admin.maintenance_mode') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert maintenance-alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('admin.maintenance_alert') }}
                                </div>
                                <div class="row">
                                    <div class="mb-4 col-md-12">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="mb-1">{{ __('admin.maintenance_status') }}</h6>
                                                        <small class="text-muted">{{ __('admin.enable_disable_maintenance') }}</small>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="maintenance_mode" value="0">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="maintenance_mode" name="maintenance_mode" value="1"
                                                            @checked((bool)($setting['maintenance_mode'] ?? false))>
                                                        <label class="form-check-label fw-bold" for="maintenance_mode" id="maintenance-status">
                                                            {{ ($setting['maintenance_mode'] ?? false) ? __('admin.enabled') : __('admin.disabled') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">{{ __('admin.maintenance_title') }}</label>
                                        <input type="text" class="form-control" name="maintenance_title"
                                            value="{{ $setting['maintenance_title'] ?? __('admin.maintenance_title_default') }}"
                                            placeholder="{{ __('admin.maintenance_title_placeholder') }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">{{ __('admin.maintenance_message') }}</label>
                                        <textarea class="form-control" name="maintenance_message" rows="4"
                                            placeholder="{{ __('admin.maintenance_message_placeholder') }}">{{ $setting['maintenance_message'] ?? '' }}</textarea>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('admin.maintenance_end_time') }}</label>
                                        <input type="datetime-local" class="form-control" name="maintenance_end_time"
                                            value="{{ $setting['maintenance_end_time'] ?? '' }}">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('admin.maintenance_contact_email') }}</label>
                                        <input type="email" class="form-control" name="maintenance_contact_email"
                                            value="{{ $setting['maintenance_contact_email'] ?? $setting['site_email'] ?? '' }}"
                                            placeholder="contact@sahra.com">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="allow_admin_access" value="1"
                                                {{ ($setting['allow_admin_access'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('admin.allow_admin_access') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="mt-4 card settings-card">
                    <div class="card-body">
                        <div class="flex-wrap d-flex justify-content-between align-items-center">
                            <div class="mb-2 mb-md-0">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    {{ __('admin.will_save_all_changes') }}
                                </small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="location.reload()">
                                    <i class="fas fa-undo me-1"></i>{{ __('admin.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    {{ __('admin.save_settings') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تفعيل التبويبات Bootstrap 5
            const triggerTabList = document.querySelectorAll('button[data-bs-toggle="pill"]');
            triggerTabList.forEach(triggerTab => {
                const tabTrigger = new bootstrap.Tab(triggerTab);

                triggerTab.addEventListener('click', function (event) {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });

            // حفظ التبويب النشط
            const activeTabKey = 'activeSettingsTab';
            const savedTab = localStorage.getItem(activeTabKey);

            if (savedTab) {
                const savedTabButton = document.querySelector(`button[data-bs-target="${savedTab}"]`);
                if (savedTabButton) {
                    const tab = new bootstrap.Tab(savedTabButton);
                    tab.show();
                }
            }

            // حفظ التبويب عند التغيير
            triggerTabList.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    const targetTab = event.target.getAttribute('data-bs-target');
                    localStorage.setItem(activeTabKey, targetTab);
                });
            });

            // تحديث نص حالة الصيانة
            const maintenanceToggle = document.getElementById('maintenance_mode');
            const maintenanceStatus = document.getElementById('maintenance-status');

            if (maintenanceToggle && maintenanceStatus) {
                maintenanceToggle.addEventListener('change', function() {
                    maintenanceStatus.textContent = this.checked ? '{{ __('admin.enabled') }}' : '{{ __('admin.disabled') }}';
                    maintenanceStatus.className = this.checked ? 'form-check-label fw-bold text-warning' : 'form-check-label fw-bold text-success';
                });
            }

            // تحديد حد أقصى للملفات المرفوعة (2MB)
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    if (this.files[0]) {
                        const fileSize = this.files[0].size;
                        const maxSize = 2 * 1024 * 1024; // 2MB

                        if (fileSize > maxSize) {
                            alert('{{ __('admin.file_size_too_large') }}');
                            this.value = '';
                            return false;
                        }

                        // معاينة الصورة
                        if (this.files[0].type.startsWith('image/')) {
                            previewImage(this);
                        }
                    }
                });
            });

            // دالة معاينة الصورة
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // العثور على منطقة المعاينة
                        let preview = input.parentElement.parentElement.querySelector('.preview-image img');
                        if (preview) {
                            preview.src = e.target.result;
                        } else {
                            // إنشاء معاينة جديدة إذا لم تكن موجودة
                            const previewDiv = document.createElement('div');
                            previewDiv.className = 'mt-3 preview-image text-center';
                            previewDiv.innerHTML = `
                                <img src="${e.target.result}" alt="preview" style="max-height:80px;" class="img-fluid">
                                <p class="mt-2 mb-0 text-muted small">{{ __('admin.new_image_preview') }}</p>
                            `;
                            input.parentElement.appendChild(previewDiv);
                        }
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // التحقق من صحة الروابط
            const urlInputs = document.querySelectorAll('input[type="url"]');
            urlInputs.forEach(function(input) {
                input.addEventListener('blur', function() {
                    if (this.value && !isValidURL(this.value)) {
                        this.classList.add('is-invalid');

                        // إضافة رسالة خطأ
                        let feedback = this.parentElement.querySelector('.invalid-feedback');
                        if (!feedback) {
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = '{{ __('admin.please_enter_valid_link') }}';
                            this.parentElement.appendChild(feedback);
                        }
                    } else {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentElement.querySelector('.invalid-feedback');
                        if (feedback) feedback.remove();
                    }
                });
            });

            // دالة التحقق من صحة الرابط
            function isValidURL(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }

            // تحديث معاينة الألوان
            const colorInputs = document.querySelectorAll('input[type="color"]');
            colorInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    const colorValue = this.nextElementSibling;
                    if (colorValue) {
                        colorValue.textContent = this.value;
                    }
                });
            });

            // التحقق من النموذج قبل الإرسال
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredInputs = form.querySelectorAll('[required]');
                    let isValid = true;

                    requiredInputs.forEach(input => {
                        if (!input.value.trim()) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        alert('{{ __('admin.please_fill_all_required_fields') }}');
                        return false;
                    }
                });
            }
        });
    </script>
    @endpush
</x-dashboard-layout>
