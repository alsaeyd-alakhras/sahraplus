<footer class="bg-[#0f0f0f] text-white pt-10 pb-6 px-6 mt-12 border-t border-gray-800">
    <div class="grid grid-cols-1 gap-8 mx-auto max-w-screen-xl md:grid-cols-3">

        <!-- الشعار والوصف -->
        <div>
            @php
                $locale = app()->getLocale();
                $siteName = $locale === 'ar'
                    ? ($settings['site_name_ar'] ?? 'سهرة بلس')
                    : ($settings['site_name_en'] ?? 'Sahra Plus');

                $logoPath = $settings['logo_url'] ?? null;
                $logoUrl = null;

                if ($logoPath) {
                    if (\Illuminate\Support\Str::startsWith($logoPath, ['http://', 'https://'])) {
                        $logoUrl = $logoPath;
                    } else {
                        $logoUrl = asset('storage/' . ltrim($logoPath, '/'));
                    }
                }

                $defaultDescription = 'منصة ترفيهية لمشاهدة أحدث الأفلام والمسلسلات بجودة عالية وبدون إعلانات مزعجة. استمتع بمحتوى متجدد يوميًا أينما كنت.';
                $description = $locale === 'ar'
                    ? ($settings['site_description_ar'] ?? $defaultDescription)
                    : ($settings['site_description_en'] ?? config('settings.app_description'));
            @endphp
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="w-32" />
            @else
                <h1 class="mb-3 text-2xl font-extrabold text-red-600">{{ $siteName }}</h1>
            @endif
            <p class="text-sm leading-relaxed text-gray-400">
                {{ $description }}
            </p>
        </div>

        <!-- روابط مهمة -->
        <div class="md:text-center">
            <h3 class="mb-3 text-lg font-semibold">روابط سريعة</h3>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><a href="#" class="transition hover:text-red-400">الرئيسية</a></li>
                <li><a href="#" class="transition hover:text-red-400">الأفلام</a></li>
                <li><a href="#" class="transition hover:text-red-400">المسلسلات</a></li>
                <li><a href="#" class="transition hover:text-red-400">بث مباشر</a></li>
                <li><a href="#" class="transition hover:text-red-400">اتصل بنا</a></li>
            </ul>
        </div>

        <!-- روابط التطبيقات -->
        <div class="md:text-left">
            <h3 class="mb-3 text-lg font-semibold">حمل التطبيق</h3>
            <div class="flex flex-col space-y-3">
                @php
                    $androidAppUrl = $settings['android_app_url'] ?? null;
                    $iosAppUrl     = $settings['ios_app_url'] ?? null;
                @endphp
                <a href="{{ $androidAppUrl ?: '#' }}"
                    class="inline-flex items-center px-4 py-2 space-x-2 text-sm text-white bg-gray-800 rounded-md transition hover:bg-gray-700 rtl:space-x-reverse">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.6 14.3c-.1 0-.1 0 0 0-1.3 0-2.3.6-2.9 1.1-.6.5-1.3 1.1-2.3 1.1s-1.7-.6-2.3-1.1c-.6-.5-1.6-1.1-2.9-1.1h-.1C4.3 14.3 2 16.2 2 18.6v.4c0 .6.5 1 1 1h18c.5 0 1-.4 1-1v-.4c0-2.4-2.3-4.3-4.4-4.3z" />
                    </svg>
                    <span>تحميل للأندرويد</span>
                </a>
                <a href="{{ $iosAppUrl ?: '#' }}"
                    class="inline-flex items-center px-4 py-2 space-x-2 text-sm text-white bg-gray-800 rounded-md transition hover:bg-gray-700 rtl:space-x-reverse">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.5 3.4c-1.4.1-3 1.1-3.6 2.3-.8 1.4-.6 3.3.3 4.5.9 1.2 2.3 2 3.6 1.9 1.2-.1 2.7-.8 3.3-1.8.7-1.2.8-2.9.3-4.3-.6-1.5-2-2.5-3.9-2.6zM12 8c-1.6 0-2.9 1.2-3.4 2.8C8 13 8.9 15 10.7 16c1.8.9 3.6 0 4.4-1.7C15.9 12.5 14.3 8 12 8z" />
                    </svg>
                    <span>تحميل للآيفون</span>
                </a>
            </div>
        </div>
    </div>

    <!-- الحقوق -->
    <div class="pt-4 mt-10 text-xs text-center text-gray-500 border-t border-gray-800">
        @php
            $locale = app()->getLocale();
            $siteName = $locale === 'ar'
                ? ($settings['site_name_ar'] ?? 'سهرة بلس')
                : ($settings['site_name_en'] ?? 'Sahra Plus');
        @endphp
        &copy; {{ date('Y') }} {{ $siteName }}. جميع الحقوق محفوظة.
    </div>
</footer>
