<x-front-layout>
    <!-- Hero Section -->
    <section id="hero" class="overflow-hidden relative h-screen">
        <!-- Slides -->
        @foreach(($heroItems ?? collect()) as $item)
        <div class="absolute inset-0 {{ $loop->first ? 'opacity-100' : 'opacity-0' }} hero-slide">
            <img src="{{ $item['backdrop'] ?? $item['poster'] ?? asset('assets-site/images/slider/slider1.avif') }}" alt="Hero Background"
                class="object-cover absolute inset-0 w-full h-full" />
            <video src="{{asset('assets-site/videos/mov_bbb.mp4')}}" class="hidden object-cover absolute inset-0 w-full h-full" playsinline></video>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        @endforeach


        <!-- Mute/Unmute Button -->
        <button id="muteBtn" class="absolute left-10 top-28 z-20 p-3 text-white rounded-full bg-black/60"
            data-state="muted">
            <svg class="w-6 h-6 mute-icon" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z">
                </path>
            </svg>
        </button>

        <!-- Hero Content -->
        <div class="flex relative z-10 items-center h-full">
            <div class="container px-6 mx-auto">
                <div
                    class="max-w-[25rem] opacity-80 transition-all duration-500 ease-in-out transform translate-x-0 hero-content hover:opacity-100 hover:-translate-x-10">
                    <div class="mb-8 h-[80px] logo-wrapper transition-all duration-500 ease-in-out">
                        @php $currentHero = ($heroItems ?? collect())->first(); @endphp
                        <img src="{{ $currentHero['logo'] ?? asset('assets-site/images/logos/logo1.avif') }}" alt="logo"
                            class="object-contain h-full transition-all duration-300 hover:scale-125" />
                    </div>
                    <div class="text-base text-gray-400 transition-all duration-300 episode animate-slide-up">
                        {{ $currentHero['title'] ?? '' }}
                    </div>
                    <div class="flex items-center my-4 space-x-2 rtl:space-x-reverse animate-slide-up">
                        <button
                            class="flex items-center px-2 py-2 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <a href="{{ $currentHero['url'] ?? '#' }}"
                            class="flex items-center px-8 py-2 space-x-2 text-lg font-bold text-white rounded-lg transition-all duration-300 bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <span>شاهد الآن</span>
                        </a>
                    </div>
                    <p
                        class="mb-6 max-w-xl text-xl leading-relaxed text-gray-200 transition-all duration-300 md:text-lg animate-slide-up description">
                        {{ $currentHero['description'] ?? '' }}
                    </p>
                    <div
                        class="flex flex-wrap items-center mb-6 space-x-3 text-sm text-gray-400 rtl:space-x-reverse tags animate-slide-up">
                        @if(!empty($currentHero['tags']))
                            @foreach($currentHero['tags'] as $tag)
                                <span class="px-2 py-1 rounded bg-white/10">{{ $tag }}</span>
                            @endforeach
                        @endif
                    </div>

                </div>


            </div>
        </div>

        <!-- Hero Navigation Dots -->
        <div class="flex absolute bottom-8 left-1/2 z-20 space-x-3 transform -translate-x-1/2 rtl:space-x-reverse">
            @foreach(($heroItems ?? collect()) as $item)
                <button class="w-full h-[72px] hero-dot opacity-75 hover:opacity-100 hover:scale-110 transition-all duration-300">
                    <img src="{{ $item['logo'] ?? $item['poster'] ?? asset('assets-site/images/logos/logo1.avif') }}" alt="logo{{ $loop->iteration }}" class="object-contain w-full h-full" />
                </button>
            @endforeach
        </div>
    </section>

    <!-- horizontal slider -->
    <div class="overflow-visible mb-6 px-4 py-6 mx-auto max-w-[95%]">
        <!-- عنوان القسم -->
        <h2 class="mb-4 text-2xl font-bold text-right">{{ optional($categoryMovies['category'] ?? null)->name ?? 'إصدارات جديدة' }}</h2>

        <!-- سلايدر Swiper -->
        <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
            <div class="swiper-wrapper">
                @if(!empty($categoryMovies['items']))
                    @foreach($categoryMovies['items'] as $m)
                    <div class="swiper-slide">
                        <div class="movie-slider-card">
                            <img src="{{ $m['poster'] ?? 'https://placehold.co/320x190' }}" alt="{{ $m['title'] }}" class="object-cover w-full rounded-md aspect-video">
                            <div class="movie-slider-details">
                                <h3 class="text-lg font-bold">{{ $m['title'] }}</h3>
                                <div class="movie-slider-line">
                                    @php $tags = $m['tags'] ?? []; @endphp
                                    @if(count($tags) >= 1)
                                        <span>{{ $tags[0] }}</span>
                                    @endif
                                    @if(count($tags) >= 2)
                                        <span class="text-green-400">•</span>
                                        <span>{{ $tags[1] }}</span>
                                    @endif
                                </div>
                                <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                    {{ $m['title'] }}
                                </div>
                                <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                    <button
                                        class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ $m['url'] ?? '#' }}"
                                        class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        <span>شاهد الآن</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- الأسهم -->
            <div class="text-white swiper-button-next"></div>
            <div class="text-white swiper-button-prev"></div>
        </div>
    </div>
    <div class="overflow-visible mb-6 px-4 py-6 mx-auto max-w-[95%]">
        <!-- عنوان القسم -->
        <h2 class="mb-4 text-2xl font-bold text-right">{{ optional($categorySeries['category'] ?? null)->name ?? 'أفلام كورية' }}</h2>

        <!-- سلايدر Swiper -->
        <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
            <div class="swiper-wrapper">
                @if(!empty($categorySeries['items']))
                    @foreach($categorySeries['items'] as $s)
                    <div class="swiper-slide">
                        <div class="movie-slider-card">
                            <img src="{{ $s['poster'] ?? 'https://placehold.co/320x190' }}" alt="{{ $s['title'] }}" class="object-cover w-full rounded-md aspect-video">
                            <div class="movie-slider-details">
                                <h3 class="text-lg font-bold">{{ $s['title'] }}</h3>
                                <div class="movie-slider-line">
                                    @php $tags = $s['tags'] ?? []; @endphp
                                    @if(count($tags) >= 1)
                                        <span>{{ $tags[0] }}</span>
                                    @endif
                                    @if(count($tags) >= 2)
                                        <span class="text-green-400">•</span>
                                        <span>{{ $tags[1] }}</span>
                                    @endif
                                </div>
                                <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                    {{ $s['title'] }}
                                </div>
                                <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                    <button
                                        class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ $s['url'] ?? '#' }}"
                                        class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        <span>شاهد الآن</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- الأسهم -->
            <div class="text-white swiper-button-next"></div>
            <div class="text-white swiper-button-prev"></div>
        </div>
    </div>

    <!-- متابعة المشاهدة -->
    <div class="overflow-visible mb-6 px-4 py-6 mx-auto max-w-[95%]">
        <!-- عنوان القسم -->
        <h2 class="mb-4 text-2xl font-bold text-right">متابعة المشاهدة</h2>

        <!-- سلايدر Swiper -->
        <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
            <div class="swiper-wrapper">
                @auth
                    @if(($continueWatching ?? collect())->count())
                        @foreach($continueWatching as $cw)
                        <div class="swiper-slide">
                            <div class="movie-slider-card">
                                <div class="relative">
                                    <img src="{{ $cw['poster'] ?? 'https://placehold.co/320x190' }}" alt="{{ $cw['title'] }}" class="object-cover w-full rounded-md aspect-video">
                                    <!-- شريط المدة -->
                                    <div class="absolute bottom-0 left-0 h-1 bg-teal-400" style="width: {{ $cw['progress_pct'] ?? 0 }}%"></div>
                                    <!-- زر التشغيل فوق الصورة -->
                                    <button class="flex absolute inset-0 justify-end items-end p-4 gap-[8px]">
                                        <span class="text-white md:vw-text-[10] text-[10px]">{{ $cw['time'] ?? '00:00:00' }}</span>
                                        <span class="relative flex shrink-0 items-center justify-center text-shahidGray h-[24px] w-[24px] md:vw-h-[24] md:vw-w-[24]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="absolute inset-0 w-full h-full group-hover/media-btn:hidden">
                                                <defs>
                                                    <linearGradient id="linearGradient-0.5315750833316147" x1="0%" x2="100%" y1="50%" y2="50%">
                                                        <stop offset="0%" stop-color="#0C9"></stop>
                                                        <stop offset="100%" stop-color="#09F"></stop>
                                                    </linearGradient>
                                                </defs>
                                                <g fill="none" fill-opacity="0.4" fill-rule="evenodd" stroke="none" stroke-width="1">
                                                    <rect width="31" height="31" x="0.5" y="0.5" fill="#181D25" stroke="url(#linearGradient-0.5315750833316147)" rx="15.5"></rect>
                                                </g>
                                            </svg>
                                            <span class="hidden absolute inset-0 w-full h-full rounded-full bg-primary group-hover/media-btn:inline-block"></span>
                                            <img alt="playIcon" title="لعب" class="relative h-[16px] w-[16px] 2xl:vw-h-[14] 2xl:vw-w-[14]" src="https://shahid.mbc.net/staticFiles/production/static/images/shdicons-24-2-px-player-play-filled.svg">
                                        </span>
                                    </button>
                                </div>
                                <div class="movie-slider-details">
                                    <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                        {{ $cw['title'] }}
                                    </div>
                                    <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                        <div class="flex gap-x-4">
                                            <!-- زر: المزيد من المعلومات -->
                                            <a href="{{ $cw['url'] ?? '#' }}" class="flex gap-1 items-center transition-all duration-200 group hover:text-white">
                                                <span class="flex relative justify-center items-center w-6 h-6 rounded-full transition-transform duration-300 shrink-0 text-shahidGray bg-white/5 group-hover:rotate-12 group-hover:scale-110">
                                                    <i class="fa-solid fa-info"></i>
                                                </span>
                                                <span class="text-xs truncate transition-colors duration-200 text-shahidGray group-hover:text-white">
                                                    المزيد من المعلومات
                                                </span>
                                            </a>
                                            <!-- زر: إزالة -->
                                            <button class="flex gap-1 items-center transition-all duration-200 group hover:text-white">
                                                <span class="relative flex shrink-0 items-center justify-center text-shahidGray h-6 w-6 rounded-full bg-white/5 group-hover:rotate-[18deg] group-hover:scale-110 transition-transform duration-300">
                                                    <i class="fa-solid fa-trash"></i>
                                                </span>
                                                <span class="text-xs truncate transition-colors duration-200 text-shahidGray group-hover:text-white">
                                                    إزالة
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                @endauth
            </div>

            <!-- الأسهم -->
            <div class="text-white swiper-button-next"></div>
            <div class="text-white swiper-button-prev"></div>
        </div>
    </div>

    <!-- vertical slider -->
    <div class="overflow-visible mb-6 px-4 py-6 mx-auto max-w-[95%]">
        <h2 class="mb-4 text-2xl font-bold text-right">
            الأكثر إكمالاً حتى آخر ثانية
        </h2>

        <div class="overflow-visible relative swiper mySwiper-vertical">
            <div class="swiper-wrapper">
                @foreach(($topViewed ?? collect()) as $tv)
                <div class="swiper-slide w-[140px]">
                    <div class="movie-vertical-card">
                        <img src="{{ $tv['poster'] ?? 'https://placehold.co/270x400' }}" alt="{{ $tv['title'] }}" class="object-cover w-full rounded-md">
                        <div class="text-xs text-center movie-vertical-details">
                            <span class="block font-bold text-white">{{ $tv['title'] }}</span>
                            <span class="block mt-1 text-blue-400">حلقة متوفرة مجاناً</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-white swiper-button-next"></div>
            <div class="text-white swiper-button-prev"></div>
        </div>
    </div>
    <!-- قسم التصنيفات (اكتشف المزيد) -->
    <div class="mb-6 px-4 py-6 mx-auto max-w-[95%] text-white">
        <h2 class="mb-4 text-2xl font-bold text-right">اكتشف المزيد</h2>

        <!-- Swiper container -->
        <div class="overflow-visible pb-8 swiper mySwiper-categories">
            <div class="swiper-wrapper">
                @foreach(($categoriesList ?? collect()) as $cat)
                <div class="swiper-slide">
                    <a href="{{ route('site.categories.show', $cat) }}" class="overflow-hidden relative w-64 h-64 rounded-lg transition-transform duration-300 transform cursor-pointer hover:scale-125 group">
                        <img src="{{ $cat->image_full_url }}" alt="{{ $cat->name ?? $cat->name_ar ?? '' }}" class="object-cover w-full h-full transition-all duration-300 group-hover:brightness-75">
                    </a>
                </div>
                @endforeach
            </div>

            <!-- Navigation arrows -->
            <div class="text-white swiper-button-next"></div>
            <div class="text-white swiper-button-prev"></div>
        </div>
    </div>
    <div class="mb-6 px-4 py-6 mx-auto max-w-[95%]">
        <h2 class="mb-4 text-2xl font-bold text-right">أفضل 10 أعمال</h2>

        <div class="overflow-visible relative swiper best10Swiper">
            <div class="swiper-wrapper">
                @foreach(($top10 ?? collect()) as $index => $item)
                <div class="swiper-slide">
                    <div class="overflow-hidden relative rounded-md group">
                        <img src="{{ $item['poster'] ?? 'https://placehold.co/400x600' }}" alt="Top {{ $index + 1 }}" class="object-cover w-full h-auto rounded-md transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute top-0 right-0 z-10 px-3 py-1 text-xs font-bold text-white bg-pink-600 rounded-bl-md">
                            TOP<br>{{ $index + 1 }}
                        </div>
                        <div class="py-2 text-sm text-center text-white bg-gray-900">حلقات متوفرة مجانًا</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- الأسهم -->
            <div class="text-white swiper-button-next"></div>
            <div class="text-white swiper-button-prev"></div>
        </div>
    </div>

</x-front-layout>
