<x-front-layout>
    @php
        $title = 'title_' . app()->getLocale();
        $description = 'description_' . app()->getLocale();

        // متغيرات التحكم في الوصول
        $requireLogin = $movie->require_login ?? false;
        $requireSubscription = $movie->require_subscription ?? false;
        $isAuthenticated = auth()->check();
        $hasSubscription = $isAuthenticated ? auth()->user()->has_active_subscription ?? true : false;
    @endphp

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
        <style>
            #videoControls button {
                background: rgba(255, 255, 255, 0.1);
                padding: 10px;
                border-radius: 50%;
                color: white;
                font-size: 18px;
                transition: background 0.3s;
            }

            #videoControls button:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            .quality-selector {
                position: absolute;
                bottom: 60px;
                right: 20px;
                z-index: 25;
            }

            .subtitle-controls {
                position: absolute;
                bottom: 60px;
                right: 160px;
                z-index: 25;
            }

            .quality-dropdown {
                background: rgba(0, 0, 0, 0.8);
                border-radius: 4px;
                padding: 8px;
                min-width: 120px;
            }

            .quality-option {
                padding: 4px 8px;
                cursor: pointer;
                border-radius: 2px;
                transition: background 0.2s;
                color: white;
                font-size: 14px;
            }

            .quality-option:hover {
                background: rgba(255, 255, 255, 0.2);
            }

            .quality-option.active {
                background: #ef4444;
            }

            .subtitle-text {
                position: absolute;
                bottom: 80px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 8px 16px;
                border-radius: 4px;
                text-align: center;
                font-size: 16px;
                max-width: 80%;
                line-height: 1.4;
                z-index: 20;
            }

            .hero-slide img,
            .hero-slide video {
                transition: opacity 0.8s ease-in-out;
            }

            .tab {
                transition: all 0.3s ease;
            }

            .tab.active {
                background-color: #ef4444 !important;
            }

            .tab-content {
                display: none;
                opacity: 0;
                transform: translateY(10px);
                transition: all 0.3s ease;
            }

            .tab-content.active {
                display: block;
                opacity: 1;
                transform: translateY(0);
            }
        </style>
    @endpush

    <!-- Hero Section -->
    <section id="hero" class="overflow-hidden relative h-screen">
        <div class="absolute inset-0 opacity-100 hero-slide">
            <img id="heroImage" src="{{ $movie->backdrop_full_url }}" alt="Hero Background"
                class="object-cover absolute inset-0 w-full h-full" />
            @if ($movie->trailer_full_url)
                <video id="heroVideo" src="{{ $movie->trailer_full_url }}"
                    class="hidden object-cover absolute inset-0 w-full h-full" playsinline muted loop></video>
            @endif
            <div class="absolute inset-0 hero-gradient"></div>
        </div>

        @if ($movie->trailer_full_url)
            <button id="muteBtn" class="absolute left-10 top-28 z-20 p-3 text-white rounded-full bg-black/60"
                data-state="muted">
                <svg class="w-6 h-6 mute-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z">
                    </path>
                </svg>
            </button>
        @endif

        <div class="flex relative z-10 items-end pb-20 h-full">
            <div class="container px-6 mx-auto">
                <div class="max-w-2xl text-white">
                    <div class="h-[60px] mb-4">
                        <img src="{{ $movie->poster_full_url }}" alt="{{ $movie->$title }}"
                            class="object-contain h-full" />
                    </div>
                    <div class="mb-2 text-sm text-gray-300">{{ $movie->$title }}</div>
                    <p class="mb-4 text-base leading-relaxed text-gray-200 md:text-lg">{{ $movie->$description }}</p>
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm font-bold text-yellow-400">⭐ {{ $movie->imdb_rating }}</div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        <button id="watchNow" data-require-login="{{ $requireLogin ? 'true' : 'false' }}"
                            data-require-subscription="{{ $requireSubscription ? 'true' : 'false' }}"
                            data-is-authenticated="{{ $isAuthenticated ? 'true' : 'false' }}"
                            data-has-subscription="{{ $hasSubscription ? 'true' : 'false' }}"
                            class="flex items-center px-6 py-2 text-sm font-bold text-white rounded-lg transition-all bg-fire-red hover:bg-red-700">
                            <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            {{ __('site.watch_now') }}
                        </button>

                        <button id="addToWatchlist" data-movie-id="{{ $movie->id }}"
                            class="flex gap-2 items-center px-5 py-2 text-sm font-bold text-white bg-gray-700 rounded-lg hover:bg-gray-600 transition-all">
                            <i class="fas fa-plus"></i>
                            <span>{{ __('site.add_to_watchlist') }}</span>
                        </button>

                        <div class="inline-block relative" id="share-container">
                            <button id="copy-link"
                                class="flex gap-2 items-center px-5 py-2 text-sm text-white transition-all duration-300 hover:text-sky-400">
                                <i class="fas fa-share-alt"></i>
                                {{ __('site.share') }}
                            </button>
                            <div id="copy-alert"
                                class="absolute right-0 px-3 py-1 mt-2 text-xs text-green-500 bg-white bg-opacity-10 rounded-md shadow opacity-0 transition-opacity duration-300 pointer-events-none">
                                {{ __('site.copied') }} ✅
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex relative z-10 items-center h-full">
            <div class="container px-6 mx-auto">
                <div
                    class="max-w-[25rem] opacity-80 transition-all duration-500 ease-in-out transform translate-x-0 hero-content hover:opacity-100 hover:-translate-x-10">
                    <div class="mb-8 h-[80px] logo-wrapper transition-all duration-500 ease-in-out">
                        <img src="./assets/images/logos/logo1.avif" alt="logo"
                            class="object-contain h-full transition-all duration-300 hover:scale-125" />
                    </div>
                    <div class="text-base text-gray-400 transition-all duration-300 episode animate-slide-up">الموسم 1،
                        الحلقة 1</div>
                    <div class="flex items-center my-4 space-x-2 rtl:space-x-reverse animate-slide-up">
                        <button
                            class="flex items-center px-2 py-2 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <button
                            class="watchNowBtn flex items-center px-8 py-2 space-x-2 text-lg font-bold text-white rounded-lg transition-all duration-300 bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <span>شاهد الآن</span>
                        </button>
                    </div>
                    <p
                        class="mb-6 max-w-xl text-xl leading-relaxed text-gray-200 transition-all duration-300 md:text-lg animate-slide-up description">
                        بعد خيانة أصدقائه والمرأة التي أحبها، يجد مجد نفسه خلف القضبان...</p>
                    <div
                        class="flex flex-wrap items-center mb-6 space-x-3 text-sm text-gray-400 rtl:space-x-reverse tags animate-slide-up">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Player -->
    <section id="playerSection" class="hidden fixed inset-0 z-[9999] bg-black">
        <div class="relative w-full h-full">
            <video id="watchVideo" src="{{ $movie->videoFiles->first()->url }}" class="object-cover z-10 w-full h-full" playsinline
                data-intro="{{ $movie->intro_skip_time ?? 0 }}"></video>
            <div id="subtitleText" class="subtitle-text hidden"></div>

            @if ($movie->intro_skip_time && $movie->intro_skip_time > 0)
                <button id="skipIntroBtn"
                    class="hidden absolute right-5 bottom-16 z-30 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded transition hover:bg-red-700">
                    ⏩ {{ __('site.skip_intro') }}
                </button>
            @endif

            @if ($movie->videoFiles && $movie->videoFiles->count() > 0)
                <div class="quality-selector pointer-events-auto">
                    <div class="relative">
                        <button id="qualityBtn" class="p-2 rounded bg-black/50 hover:bg-black/70 text-white text-sm">
                            <i class="fas fa-cog mr-1"></i>
                            <span id="currentQuality">{{ __('site.auto') }}</span>
                            <i class="fas fa-chevron-up ml-1"></i>
                        </button>
                        <div id="qualityDropdown" class="quality-dropdown absolute bottom-full right-0 mb-2 hidden">
                            <div class="quality-option active" data-quality="auto">{{ __('site.auto') }}</div>
                            @foreach ($movie->videoFiles as $videoFile)
                                <div class="quality-option" data-quality="{{ $videoFile->quality }}"
                                    data-url="{{ $videoFile->file_url }}">{{ $videoFile->quality }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($movie->subtitles && $movie->subtitles->count() > 0)
                <div class="subtitle-controls pointer-events-auto">
                    <div class="relative">
                        <button id="subtitleBtn" class="p-2 rounded bg-black/50 hover:bg-black/70 text-white text-sm">
                            <i class="fas fa-closed-captioning mr-1"></i>
                            <span id="currentSubtitle">{{ __('site.off') }}</span>
                        </button>
                        <div id="subtitleDropdown" class="quality-dropdown absolute bottom-full right-0 mb-2 hidden">
                            <div class="quality-option active" data-subtitle="off">{{ __('site.off') }}</div>
                            @foreach ($movie->subtitles as $subtitle)
                                <div class="quality-option" data-subtitle="{{ $subtitle->language }}"
                                    data-url="{{ $subtitle->file_url }}">{{ $subtitle->label }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div id="videoControls"
                class="flex absolute inset-0 z-20 flex-col justify-between p-4 text-white pointer-events-none">
                <div class="flex justify-between items-center pointer-events-auto">
                    <button id="toggleMute" class="p-2 rounded bg-black/50 hover:bg-black/70"><i
                            class="fas fa-volume-up"></i></button>
                    <button id="exitFullscreen" class="p-2 rounded bg-black/50 hover:bg-black/70"><i
                            class="fas fa-times"></i></button>
                </div>

                <div class="flex gap-4 items-center w-full pointer-events-auto" dir="ltr">
                    <span id="currentTime" class="w-12 text-xs text-right text-gray-300">0:00</span>
                    <input type="range" id="progressBar" min="0" value="0"
                        class="w-full h-1 bg-gray-600 rounded cursor-pointer accent-sky-400" />
                    <span id="duration" class="w-12 text-xs text-left text-gray-300">0:00</span>
                    <div class="flex gap-3 items-center pl-4" dir="rtl">
                        <button id="rewind" class="p-2 rounded bg-black/50 hover:bg-black/70"><i
                                class="fas fa-undo"></i></button>
                        <button id="togglePlay" class="p-3 rounded-full bg-black/50 hover:bg-black/70"><i
                                id="playIcon" class="fas fa-pause"></i></button>
                        <button id="forward" class="p-2 rounded bg-black/50 hover:bg-black/70"><i
                                class="fas fa-redo"></i></button>
                    </div>
                </div>
            </div>

            <div id="nextEpisodesSlider"
                class="transition-all duration-700 absolute bottom-0 left-0 w-full z-[9999] bg-black bg-opacity-70 pointer-events-auto hidden"
                style="transform: translateY(100%); opacity: 0;">
                <div class="overflow-visible mb-6 px-4 py-6 mx-auto max-w-[95%]">
                    <h2 class="mb-4 text-2xl font-bold text-right">أفلام ذات صلة</h2>
                    <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
                        <div class="swiper-wrapper">
                            <script>
                                const movies2 = ["Hello+World", "Movie+1", "Guardians", "Lost+City", "Action+Show", "Romance+Story", "The+Heist",
                                    "Last+Stand", "Arab+Drama", "Old+Legends"
                                ];
                                for (let title of movies2) {
                                    document.write(`
                <div class="swiper-slide">
                    <div class="movie-slider-card">
                    <img src="https://placehold.co/320x190?text=${title}" alt="${title}" class="object-cover w-full rounded-md aspect-video">
                    <div class="movie-slider-details">
                        <h3 class="text-lg font-bold">${title.replace("+", " ")}</h3>
                        <div class="movie-slider-line">
                        <span>01:46:34</span>
                        <span class="text-green-400">•</span>
                        <span>كوميدي</span>
                        <span class="text-green-400">•</span>
                        <span>رومانسي</span>
                        </div>
                        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">البطل الذي لا يريد القوة</div>
                        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                            <button class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                            <a href="#" class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                <span>شاهد الآن</span>
                            </a>
                        </div>
                    </div>
                    </div>
                </div>
                `);
                                }
                            </script>
                        </div>
                        <div class="text-white swiper-button-next"></div>
                        <div class="text-white swiper-button-prev"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabs Section -->
    <div class="container px-6 py-10 mx-auto max-w-[95%]">
        <div class="flex flex-wrap gap-2 mb-6">
            <button data-tab="episodes"
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red active">ذات
                صلة</button>
            <button data-tab="details"
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red">التفاصيل</button>
            <button data-tab="cast"
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red">الممثلين</button>
            <button data-tab="comments"
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red">التعليقات</button>
        </div>

        <div>
            <div id="episodes" class="tab-content active animate-fade-in">
                <div class="overflow-visible px-4 py-6 mx-auto mb-3">
                    <h2 class="mb-4 text-2xl font-bold text-right">أفلام ذات صلة</h2>
                    <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
                        <div class="swiper-wrapper">
                            <script>
                                const movies4 = ["Hello+World", "Movie+1", "Guardians", "Lost+City", "Action+Show", "Romance+Story", "The+Heist",
                                    "Last+Stand", "Arab+Drama", "Old+Legends"
                                ];
                                for (let title of movies4) {
                                    document.write(`
                  <div class="swiper-slide">
                    <div class="movie-slider-card">
                      <img src="https://placehold.co/320x190?text=${title}" alt="${title}" class="object-cover w-full rounded-md aspect-video">
                      <div class="movie-slider-details">
                        <h3 class="text-lg font-bold">${title.replace("+", " ")}</h3>
                        <div class="movie-slider-line">
                          <span>01:46:34</span>
                          <span class="text-green-400">•</span>
                          <span>كوميدي</span>
                          <span class="text-green-400">•</span>
                          <span>رومانسي</span>
                        </div>
                        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">البطل الذي لا يريد القوة</div>
                        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                            <button class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                            <a href="#" class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                <span>شاهد الآن</span>
                            </a>
                        </div>
                      </div>
                    </div>
                  </div>
                `);
                                }
                            </script>
                        </div>
                        <div class="text-white swiper-button-next"></div>
                        <div class="text-white swiper-button-prev"></div>
                    </div>
                </div>

                <div class="overflow-visible px-4 py-6 mx-auto mb-3">
                    <h2 class="mb-4 text-2xl font-bold text-right">أعلى المشاهدة</h2>
                    <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
                        <div class="swiper-wrapper">
                            <script>
                                const movies3 = ["Hello+World", "Movie+1", "Guardians", "Lost+City", "Action+Show", "Romance+Story", "The+Heist",
                                    "Last+Stand", "Arab+Drama", "Old+Legends"
                                ];
                                for (let title of movies3) {
                                    document.write(`
                  <div class="swiper-slide">
                    <div class="movie-slider-card">
                      <img src="https://placehold.co/320x190?text=${title}" alt="${title}" class="object-cover w-full rounded-md aspect-video">
                      <div class="movie-slider-details">
                        <h3 class="text-lg font-bold">${title.replace("+", " ")}</h3>
                        <div class="movie-slider-line">
                          <span>01:46:34</span>
                          <span class="text-green-400">•</span>
                          <span>كوميدي</span>
                          <span class="text-green-400">•</span>
                          <span>رومانسي</span>
                        </div>
                        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">البطل الذي لا يريد القوة</div>
                        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                            <button class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                            <a href="#" class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                <span>شاهد الآن</span>
                            </a>
                        </div>
                      </div>
                    </div>
                  </div>
                `);
                                }
                            </script>
                        </div>
                        <div class="text-white swiper-button-next"></div>
                        <div class="text-white swiper-button-prev"></div>
                    </div>
                </div>
            </div>

            <div id="details" class="tab-content animate-fade-in">
                <h2 class="pb-2 mb-6 text-2xl font-bold text-white border-b border-gray-600">تفاصيل الفيلم</h2>
                <div class="grid grid-cols-1 gap-6 text-gray-300 md:grid-cols-2">
                    <div class="col-span-2">
                        <p class="text-lg leading-relaxed">قصة <span class="font-semibold text-sky-400">تشويقية</span>
                            تدور حول بطل يسعى للانتقام بعد خيانة أصدقائه، حيث تتصاعد الأحداث في قالب من <span
                                class="font-semibold text-red-400">الأكشن والإثارة</span>.</p>
                    </div>
                    <div class="flex gap-3 items-center">
                        <i class="text-sky-400 fas fa-film"></i>
                        <span><span class="font-semibold text-white">التصنيف:</span> أكشن، دراما</span>
                    </div>
                    <div class="flex gap-3 items-center">
                        <i class="text-yellow-400 fas fa-clock"></i>
                        <span><span class="font-semibold text-white">المدة:</span> 45 دقيقة</span>
                    </div>
                    <div class="flex gap-3 items-center">
                        <i class="text-green-400 fas fa-calendar-alt"></i>
                        <span><span class="font-semibold text-white">سنة الإنتاج:</span> 2024</span>
                    </div>
                </div>
            </div>

            <div id="cast" class="tab-content animate-fade-in">
                <h2 class="pb-2 mb-6 text-2xl font-bold text-white border-b border-gray-600">الممثلين</h2>
                <div class="grid grid-cols-2 gap-6 text-center sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    <a href="#actor1" class="transition-transform duration-300 group hover:scale-105">
                        <div class="overflow-hidden rounded-lg shadow-md">
                            <img src="https://placehold.co/150x200" alt="ممثل 1"
                                class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                        </div>
                        <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">ممثل
                            1</span>
                    </a>
                    <a href="#actor2" class="transition-transform duration-300 group hover:scale-105">
                        <div class="overflow-hidden rounded-lg shadow-md">
                            <img src="https://placehold.co/150x200" alt="ممثل 2"
                                class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                        </div>
                        <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">ممثل
                            2</span>
                    </a>
                    <a href="#actor3" class="transition-transform duration-300 group hover:scale-105">
                        <div class="overflow-hidden rounded-lg shadow-md">
                            <img src="https://placehold.co/150x200" alt="ممثل 3"
                                class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                        </div>
                        <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">ممثل
                            3</span>
                    </a>
                    <a href="#actor4" class="transition-transform duration-300 group hover:scale-105">
                        <div class="overflow-hidden rounded-lg shadow-md">
                            <img src="https://placehold.co/150x200" alt="ممثل 4"
                                class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                        </div>
                        <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">ممثل
                            4</span>
                    </a>
                </div>
            </div>

            <div id="comments" class="tab-content animate-fade-in">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">التعليقات</h2>
                    <button id="openCommentModal"
                        class="px-4 py-1 text-sm text-white rounded transition-all bg-fire-red hover:bg-red-700">+ أضف
                        تعليق</button>
                </div>

                <div id="commentsList" class="space-y-4">
                    <div class="flex items-start p-4 bg-gray-800 bg-opacity-40 rounded-lg shadow-sm">
                        <img src="./assets/images/avatar.jpg" class="ml-3 w-10 h-10 rounded-full" alt="Avatar">
                        <div>
                            <div class="flex gap-2 items-center mb-1">
                                <p class="font-bold text-white">أنت</p>
                                <span class="text-xs text-gray-400">منذ يوم</span>
                            </div>
                            <p class="text-sm text-gray-300">فيلم رائع يستحق المشاهدة!</p>
                        </div>
                    </div>
                </div>

                <div class="my-8">
                    <div class="overflow-visible swiper mySwiper-comments">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide bg-gray-900 text-white p-4 rounded-xl w-[280px] shadow-md">
                                <div
                                    class="flex gap-4 items-start p-4 text-white bg-gray-800 bg-opacity-50 rounded-xl shadow-lg">
                                    <div class="relative">
                                        <img src="./assets/images/avatar.jpg" alt="Avatar"
                                            class="w-12 h-12 rounded-full ring-2 ring-gray-600 shadow-sm">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-white">مستخدم 1</p>
                                            <div class="text-end">
                                                <div class="flex gap-1 text-sm text-yellow-400">⭐⭐⭐⭐⭐</div>
                                                <span class="text-xs text-gray-400">منذ يوم</span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm leading-relaxed text-gray-300">فيلم رائع يستحق المشاهدة!
                                                ✨</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Modal -->
    <div id="commentModal"
        class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-[9999] hidden flex items-center justify-center">
        <div class="p-6 w-full max-w-md text-white rounded-lg shadow-lg bg-zinc-900">
            <h3 class="mb-4 text-lg font-bold">أضف تعليقك</h3>
            <textarea id="commentInput"
                class="p-3 w-full h-24 text-sm rounded bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-sky-500"
                placeholder="اكتب تعليقك هنا..."></textarea>
            <div class="flex gap-3 justify-end mt-4">
                <button id="closeCommentModal" class="text-sm text-gray-400 hover:text-white">إلغاء</button>
                <button id="submitComment" class="px-4 py-1 text-sm bg-sky-600 rounded hover:bg-sky-700">نشر</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM Loaded - Starting initialization');

                // ===== Hero Video Management =====
                const heroImage = document.getElementById('heroImage');
                const heroVideo = document.getElementById('heroVideo');
                const muteBtn = document.getElementById('muteBtn');
                let heroVideoTimer;
                let isTrailerPlaying = false;

                function initHeroSection() {
                    console.log('Initializing hero section');
                    if (heroVideo && heroVideo.src) {
                        console.log('Trailer found, starting timer');
                        heroVideoTimer = setTimeout(() => playTrailer(), 3000);
                        heroVideo.addEventListener('ended', () => {
                            console.log('Trailer ended, returning to image');
                            stopTrailer();
                        });
                        if (muteBtn) muteBtn.addEventListener('click', toggleTrailerSound);
                    } else {
                        console.log('No trailer found');
                        if (muteBtn) muteBtn.style.display = 'none';
                    }
                }

                function playTrailer() {
                    if (heroVideo && heroImage) {
                        console.log('Playing trailer');
                        heroImage.style.opacity = '0';
                        setTimeout(() => {
                            heroImage.classList.add('hidden');
                            heroVideo.classList.remove('hidden');
                            heroVideo.style.opacity = '1';
                            heroVideo.play();
                            isTrailerPlaying = true;
                        }, 800);
                    }
                }

                function stopTrailer() {
                    if (heroVideo && heroImage) {
                        console.log('Stopping trailer');
                        heroVideo.style.opacity = '0';
                        isTrailerPlaying = false;
                        setTimeout(() => {
                            heroVideo.classList.add('hidden');
                            heroVideo.pause();
                            heroVideo.currentTime = 0;
                            heroImage.classList.remove('hidden');
                            heroImage.style.opacity = '1';
                        }, 800);
                    }
                }

                function toggleTrailerSound() {
                    if (heroVideo && isTrailerPlaying) {
                        heroVideo.muted = !heroVideo.muted;
                        const icon = muteBtn.querySelector('svg path');
                        if (heroVideo.muted) {
                            muteBtn.dataset.state = 'muted';
                            icon.setAttribute('d',
                                'M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z'
                                );
                        } else {
                            muteBtn.dataset.state = 'unmuted';
                            icon.setAttribute('d',
                                'M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z'
                                );
                        }
                    }
                }

                // ===== Watch Now Button =====
                const watchNowBtns = document.querySelectorAll('#watchNow, .watchNowBtn');
                const playerSection = document.getElementById('playerSection');
                const watchVideo = document.getElementById('watchVideo');

                function initWatchNowButtons() {
                    console.log('Initializing watch now buttons');
                    watchNowBtns.forEach(btn => btn.addEventListener('click', handleWatchNowClick));
                }

                function handleWatchNowClick(e) {
                    e.preventDefault();
                    console.log('Watch now clicked');
                    const btn = e.currentTarget;
                    const requireLogin = btn.dataset.requireLogin === 'true';
                    const requireSubscription = btn.dataset.requireSubscription === 'true';
                    const isAuthenticated = btn.dataset.isAuthenticated === 'true';
                    const hasSubscription = btn.dataset.hasSubscription === 'true';

                    console.log('Auth check:', {
                        requireLogin,
                        requireSubscription,
                        isAuthenticated,
                        hasSubscription
                    });

                    if (requireLogin && !isAuthenticated) {
                        console.log('Redirecting to login');
                        window.location.href = '/login';
                        return;
                    }
                    if (requireSubscription && (!isAuthenticated || !hasSubscription)) {
                        console.log('Redirecting to subscription');
                        window.location.href = '/subscribe';
                        return;
                    }
                    startVideoPlayer();
                }

                function startVideoPlayer() {
                    console.log('Starting video player');
                    if (playerSection && watchVideo) {
                        if (heroVideoTimer) clearTimeout(heroVideoTimer);
                        if (isTrailerPlaying) stopTrailer();
                        if (!watchVideo.src) watchVideo.src = './assets/videos/mov_bbb2.mp4';
                        playerSection.classList.remove('hidden');
                        watchVideo.classList.remove('hidden');
                        watchVideo.currentTime = 0;
                        watchVideo.volume = 1;
                        watchVideo.muted = false;
                        requestFullscreen(playerSection);
                        watchVideo.play().catch(e => console.log('Video play error:', e));
                    }
                }

                function requestFullscreen(element) {
                    if (element.requestFullscreen) element.requestFullscreen();
                    else if (element.webkitRequestFullscreen) element.webkitRequestFullscreen();
                    else if (element.msRequestFullscreen) element.msRequestFullscreen();
                }

                // ===== Video Player Controls =====
                const playBtn = document.getElementById('togglePlay');
                const playIcon = document.getElementById('playIcon');
                const rewindBtn = document.getElementById('rewind');
                const forwardBtn = document.getElementById('forward');
                const muteVideoBtn = document.getElementById('toggleMute');
                const exitBtn = document.getElementById('exitFullscreen');
                const progressBar = document.getElementById('progressBar');
                const currentTimeLabel = document.getElementById('currentTime');
                const durationLabel = document.getElementById('duration');
                const skipBtn = document.getElementById('skipIntroBtn');
                const nextSlider = document.getElementById('nextEpisodesSlider');

                function initVideoControls() {
                    console.log('Initializing video controls');
                    if (playBtn && playIcon) {
                        playBtn.addEventListener('click', () => {
                            if (watchVideo.paused) {
                                watchVideo.play();
                                playIcon.classList.replace('fa-play', 'fa-pause');
                            } else {
                                watchVideo.pause();
                                playIcon.classList.replace('fa-pause', 'fa-play');
                            }
                        });
                    }
                    if (rewindBtn) rewindBtn.addEventListener('click', () => watchVideo.currentTime -= 10);
                    if (forwardBtn) forwardBtn.addEventListener('click', () => watchVideo.currentTime += 10);
                    if (muteVideoBtn) {
                        muteVideoBtn.addEventListener('click', () => {
                            watchVideo.muted = !watchVideo.muted;
                            muteVideoBtn.innerHTML = watchVideo.muted ? '<i class="fas fa-volume-mute"></i>' :
                                '<i class="fas fa-volume-up"></i>';
                        });
                    }
                    if (exitBtn) exitBtn.addEventListener('click', () => exitFullscreen());
                    if (progressBar) progressBar.addEventListener('input', () => watchVideo.currentTime = progressBar
                        .value);
                    if (watchVideo) {
                        watchVideo.addEventListener('timeupdate', updateProgress);
                        watchVideo.addEventListener('loadedmetadata', updateDuration);
                        watchVideo.addEventListener('ended', onVideoEnded);
                    }
                    if (skipBtn) {
                        skipBtn.addEventListener('click', () => {
                            const introTime = parseFloat(watchVideo.dataset.intro);
                            if (!isNaN(introTime)) {
                                watchVideo.currentTime = introTime;
                                skipBtn.classList.add('hidden');
                            }
                        });
                    }
                    initQualitySelector();
                    initSubtitleSelector();
                }

                function updateProgress() {
                    if (progressBar) progressBar.value = watchVideo.currentTime;
                    if (currentTimeLabel) currentTimeLabel.textContent = formatTime(watchVideo.currentTime);
                    const introTime = parseFloat(watchVideo.dataset.intro);
                    if (!isNaN(introTime) && skipBtn) {
                        if (watchVideo.currentTime > 5 && watchVideo.currentTime < introTime) {
                            skipBtn.classList.remove('hidden');
                        } else {
                            skipBtn.classList.add('hidden');
                        }
                    }
                }

                function updateDuration() {
                    if (progressBar) progressBar.max = watchVideo.duration;
                    if (durationLabel) durationLabel.textContent = formatTime(watchVideo.duration);
                }

                function onVideoEnded() {
                    if (nextSlider) {
                        nextSlider.style.transform = 'translateY(0%)';
                        nextSlider.style.opacity = '1';
                        nextSlider.classList.remove('hidden');
                    }
                }

                function formatTime(sec) {
                    const minutes = Math.floor(sec / 60);
                    const seconds = Math.floor(sec % 60).toString().padStart(2, '0');
                    return `${minutes}:${seconds}`;
                }

                function exitFullscreen() {
                    if (document.fullscreenElement || document.webkitFullscreenElement || document
                        .msFullscreenElement) {
                        if (document.exitFullscreen) document.exitFullscreen();
                        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                        else if (document.msExitFullscreen) document.msExitFullscreen();
                    } else {
                        if (watchVideo) watchVideo.pause();
                        if (playerSection) playerSection.classList.add('hidden');
                        if (watchVideo) watchVideo.classList.add('hidden');
                    }
                }

                document.addEventListener('fullscreenchange', () => {
                    if (!document.fullscreenElement) {
                        if (watchVideo) watchVideo.pause();
                        if (playerSection) playerSection.classList.add('hidden');
                        if (watchVideo) watchVideo.classList.add('hidden');
                    }
                });

                // ===== Quality and Subtitle Selectors =====
                function initQualitySelector() {
                    const qualityBtn = document.getElementById('qualityBtn');
                    const qualityDropdown = document.getElementById('qualityDropdown');
                    const currentQuality = document.getElementById('currentQuality');

                    if (qualityBtn && qualityDropdown) {
                        qualityBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            qualityDropdown.classList.toggle('hidden');
                            const subtitleDropdown = document.getElementById('subtitleDropdown');
                            if (subtitleDropdown) subtitleDropdown.classList.add('hidden');
                        });

                        qualityDropdown.addEventListener('click', (e) => {
                            if (e.target.classList.contains('quality-option')) {
                                const quality = e.target.dataset.quality;
                                const url = e.target.dataset.url;
                                if (currentQuality) currentQuality.textContent = quality;
                                qualityDropdown.querySelectorAll('.quality-option').forEach(opt => opt.classList
                                    .remove('active'));
                                e.target.classList.add('active');
                                if (quality !== 'auto' && url) {
                                    const currentTime = watchVideo.currentTime;
                                    const isPlaying = !watchVideo.paused;
                                    watchVideo.src = url;
                                    watchVideo.addEventListener('loadedmetadata', () => {
                                        watchVideo.currentTime = currentTime;
                                        if (isPlaying) watchVideo.play();
                                    }, {
                                        once: true
                                    });
                                }
                                qualityDropdown.classList.add('hidden');
                            }
                        });
                    }
                }

                function initSubtitleSelector() {
                    const subtitleBtn = document.getElementById('subtitleBtn');
                    const subtitleDropdown = document.getElementById('subtitleDropdown');
                    const currentSubtitle = document.getElementById('currentSubtitle');
                    const subtitleText = document.getElementById('subtitleText');
                    let currentSubtitleTrack = null;
                    let subtitleData = [];

                    if (subtitleBtn && subtitleDropdown) {
                        subtitleBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            subtitleDropdown.classList.toggle('hidden');
                            const qualityDropdown = document.getElementById('qualityDropdown');
                            if (qualityDropdown) qualityDropdown.classList.add('hidden');
                        });

                        subtitleDropdown.addEventListener('click', async (e) => {
                            if (e.target.classList.contains('quality-option')) {
                                const subtitleLang = e.target.dataset.subtitle;
                                const subtitleUrl = e.target.dataset.url;
                                if (currentSubtitle) currentSubtitle.textContent = e.target.textContent;
                                subtitleDropdown.querySelectorAll('.quality-option').forEach(opt => opt
                                    .classList.remove('active'));
                                e.target.classList.add('active');
                                if (subtitleLang === 'off') {
                                    if (subtitleText) subtitleText.classList.add('hidden');
                                    currentSubtitleTrack = null;
                                    subtitleData = [];
                                } else if (subtitleUrl) {
                                    try {
                                        const response = await fetch(subtitleUrl);
                                        const srtContent = await response.text();
                                        subtitleData = parseSRT(srtContent);
                                        currentSubtitleTrack = subtitleLang;
                                        if (subtitleText) subtitleText.classList.remove('hidden');
                                    } catch (error) {
                                        console.error('Failed to load subtitles:', error);
                                    }
                                }
                                subtitleDropdown.classList.add('hidden');
                            }
                        });

                        if (watchVideo) {
                            watchVideo.addEventListener('timeupdate', () => {
                                if (currentSubtitleTrack && subtitleData.length > 0 && subtitleText) {
                                    const currentTime = watchVideo.currentTime;
                                    const currentSubtitle = subtitleData.find(sub => currentTime >= sub.start &&
                                        currentTime <= sub.end);
                                    if (currentSubtitle) {
                                        subtitleText.textContent = currentSubtitle.text;
                                        subtitleText.classList.remove('hidden');
                                    } else {
                                        subtitleText.classList.add('hidden');
                                    }
                                }
                            });
                        }
                    }
                }

                function parseSRT(srtText) {
                    const subtitles = [];
                    const blocks = srtText.split('\n\n');
                    blocks.forEach(block => {
                        const lines = block.trim().split('\n');
                        if (lines.length >= 3) {
                            const timeRange = lines[1];
                            const text = lines.slice(2).join('\n');
                            const times = timeRange.split(' --> ');
                            if (times.length === 2) {
                                subtitles.push({
                                    start: timeToSeconds(times[0]),
                                    end: timeToSeconds(times[1]),
                                    text: text
                                });
                            }
                        }
                    });
                    return subtitles;
                }

                function timeToSeconds(timeStr) {
                    const parts = timeStr.split(':');
                    const seconds = parts[2].split(',');
                    return parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60 + parseFloat(seconds[0] + '.' + (seconds[
                        1] || '0'));
                }

                document.addEventListener('click', () => {
                    const qualityDropdown = document.getElementById('qualityDropdown');
                    const subtitleDropdown = document.getElementById('subtitleDropdown');
                    if (qualityDropdown) qualityDropdown.classList.add('hidden');
                    if (subtitleDropdown) subtitleDropdown.classList.add('hidden');
                });

                // ===== Watchlist Functionality =====
                const addToWatchlistBtn = document.getElementById('addToWatchlist');

                function initWatchlistButton() {
                    if (addToWatchlistBtn) addToWatchlistBtn.addEventListener('click', handleWatchlistClick);
                }

                async function handleWatchlistClick(e) {
                    e.preventDefault();
                    const isAuthenticated = document.querySelector('#watchNow').dataset.isAuthenticated === 'true';
                    if (!isAuthenticated) {
                        window.location.href = '/login';
                        return;
                    }
                    const movieId = addToWatchlistBtn.dataset.movieId;
                    const btn = addToWatchlistBtn;
                    const icon = btn.querySelector('i');
                    const text = btn.querySelector('span');
                    try {
                        const response = await fetch(`/api/watchlist/${movieId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute('content') || ''
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            if (data.action === 'added') {
                                icon.classList.replace('fa-plus', 'fa-check');
                                text.textContent = 'في القائمة';
                                btn.classList.add('bg-green-600', 'hover:bg-green-700');
                                btn.classList.remove('bg-gray-700', 'hover:bg-gray-600');
                            } else {
                                icon.classList.replace('fa-check', 'fa-plus');
                                text.textContent = 'أضف للمفضلة';
                                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                                btn.classList.add('bg-gray-700', 'hover:bg-gray-600');
                            }
                            showNotification(data.message || 'تم التحديث بنجاح', 'success');
                        } else {
                            showNotification(data.message || 'حدث خطأ', 'error');
                        }
                    } catch (error) {
                        console.error('Watchlist error:', error);
                        showNotification('فشل في تحديث قائمة المشاهدة', 'error');
                    }
                }

                // ===== Share Functionality =====
                const copyLinkBtn = document.getElementById('copy-link');
                const copyAlert = document.getElementById('copy-alert');

                function initShareButton() {
                    if (copyLinkBtn) copyLinkBtn.addEventListener('click', handleShareClick);
                }

                function handleShareClick(e) {
                    e.preventDefault();
                    const url = window.location.href;
                    navigator.clipboard.writeText(url).then(() => {
                        if (copyAlert) {
                            copyAlert.classList.add('opacity-100');
                            copyAlert.classList.remove('opacity-0');
                            setTimeout(() => {
                                copyAlert.classList.remove('opacity-100');
                                copyAlert.classList.add('opacity-0');
                            }, 2500);
                        }
                    }).catch(err => {
                        console.error('فشل النسخ:', err);
                        showNotification('فشل في نسخ الرابط', 'error');
                    });
                }

                // ===== Tabs Functionality =====
                function initTabs() {
                    console.log('Initializing tabs');
                    const tabButtons = document.querySelectorAll('.tab');
                    const tabContents = document.querySelectorAll('.tab-content');
                    console.log('Found tabs:', tabButtons.length, 'contents:', tabContents.length);
                    tabButtons.forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const target = this.getAttribute('data-tab');
                            console.log('Tab clicked:', target);
                            tabButtons.forEach(b => {
                                b.classList.remove('bg-fire-red', 'active');
                            });
                            tabContents.forEach(c => {
                                c.classList.remove('active');
                                c.classList.add('hidden');
                            });
                            this.classList.add('bg-fire-red', 'active');
                            const targetElement = document.getElementById(target);
                            if (targetElement) {
                                targetElement.classList.remove('hidden');
                                targetElement.classList.add('active');
                                console.log('Activated tab:', target);
                            }
                        });
                    });
                }

                // ===== Comment Modal =====
                const openModal = document.getElementById("openCommentModal");
                const closeModal = document.getElementById("closeCommentModal");
                const modal = document.getElementById("commentModal");
                const submitComment = document.getElementById("submitComment");
                const commentInput = document.getElementById("commentInput");
                const commentsList = document.getElementById("commentsList");

                function initCommentModal() {
                    if (openModal) {
                        openModal.addEventListener("click", () => {
                            if (modal) modal.classList.remove("hidden");
                            setTimeout(() => {
                                if (commentInput) commentInput.focus();
                            }, 100);
                        });
                    }
                    if (closeModal) {
                        closeModal.addEventListener("click", () => {
                            if (modal) modal.classList.add("hidden");
                            if (commentInput) commentInput.value = "";
                        });
                    }
                    if (submitComment) submitComment.addEventListener("click", handleCommentSubmit);
                    if (modal) {
                        modal.addEventListener("click", (e) => {
                            if (e.target === modal) {
                                modal.classList.add("hidden");
                                if (commentInput) commentInput.value = "";
                            }
                        });
                    }
                }

                function handleCommentSubmit() {
                    const text = commentInput ? commentInput.value.trim() : '';
                    if (text !== "") {
                        const newComment = document.createElement("div");
                        newComment.className =
                            "flex items-start p-4 bg-gray-800 bg-opacity-40 rounded-lg shadow-sm animate-fade-in";
                        newComment.innerHTML = `
                            <img src="./assets/images/avatar.jpg" class="ml-3 w-10 h-10 rounded-full" alt="Avatar">
                            <div>
                                <div class="flex gap-2 items-center mb-1">
                                    <p class="font-bold text-white">أنت</p>
                                    <span class="text-xs text-gray-400">الآن</span>
                                </div>
                                <p class="text-sm text-gray-300">${text}</p>
                            </div>
                        `;
                        if (commentsList) commentsList.prepend(newComment);
                        if (commentInput) commentInput.value = "";
                        if (modal) modal.classList.add("hidden");
                        showNotification('تم إضافة التعليق بنجاح', 'success');
                    }
                }

                // ===== Swiper Initialization =====
                function initSwiper() {
                    console.log('Initializing Swiper');
                    new Swiper('.mySwiper-horizontal', {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        breakpoints: {
                            320: {
                                slidesPerView: 1,
                                spaceBetween: 10
                            },
                            480: {
                                slidesPerView: 2,
                                spaceBetween: 15
                            },
                            768: {
                                slidesPerView: 3,
                                spaceBetween: 20
                            },
                            1024: {
                                slidesPerView: 4,
                                spaceBetween: 20
                            },
                            1280: {
                                slidesPerView: 5,
                                spaceBetween: 20
                            }
                        }
                    });
                    new Swiper('.mySwiper-comments', {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        freeMode: true,
                    });
                }

                // ===== Notification System =====
                function showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-[10000] px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full opacity-0 ${
                        type === 'success' ? 'bg-green-600' : 
                        type === 'error' ? 'bg-red-600' : 
                        'bg-blue-600'
                        }`;
                    notification.textContent = message;
                    document.body.appendChild(notification);
                    setTimeout(() => {
                        notification.classList.remove('translate-x-full', 'opacity-0');
                    }, 100);
                    setTimeout(() => {
                        notification.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => {
                            if (document.body.contains(notification)) {
                                document.body.removeChild(notification);
                            }
                        }, 300);
                    }, 3000);
                }

                // ===== Keyboard Shortcuts =====
                document.addEventListener('keydown', function(e) {
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                    if (!playerSection.classList.contains('hidden')) {
                        switch (e.key) {
                            case ' ':
                                e.preventDefault();
                                if (playBtn) playBtn.click();
                                break;
                            case 'ArrowLeft':
                                e.preventDefault();
                                if (watchVideo) watchVideo.currentTime -= 10;
                                break;
                            case 'ArrowRight':
                                e.preventDefault();
                                if (watchVideo) watchVideo.currentTime += 10;
                                break;
                            case 'm':
                            case 'M':
                                e.preventDefault();
                                if (muteVideoBtn) muteVideoBtn.click();
                                break;
                            case 'f':
                            case 'F':
                                e.preventDefault();
                                if (document.fullscreenElement) {
                                    exitFullscreen();
                                } else {
                                    requestFullscreen(playerSection);
                                }
                                break;
                            case 'Escape':
                                if (document.fullscreenElement) {
                                    exitFullscreen();
                                }
                                break;
                        }
                    }
                });

                // ===== Auto Quality Detection =====
                function detectOptimalQuality() {
                    if (navigator.connection) {
                        const connection = navigator.connection;
                        const effectiveType = connection.effectiveType;
                        switch (effectiveType) {
                            case 'slow-2g':
                            case '2g':
                                return '360p';
                            case '3g':
                                return '480p';
                            case '4g':
                                return '720p';
                            default:
                                return '1080p';
                        }
                    }
                    return '720p';
                }

                function applyAutoQuality() {
                    const qualityDropdown = document.getElementById('qualityDropdown');
                    if (qualityDropdown) {
                        const optimalQuality = detectOptimalQuality();
                        const qualityOption = qualityDropdown.querySelector(`[data-quality="${optimalQuality}"]`);
                        if (qualityOption && qualityOption.dataset.url) {
                            console.log('Auto-selecting quality:', optimalQuality);
                            qualityOption.click();
                        }
                    }
                }

                // ===== Video Loading States =====
                function showVideoLoading() {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.id = 'video-loading';
                    loadingDiv.className =
                        'absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30';
                    loadingDiv.innerHTML = `
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
                            <p class="text-white">جاري تحميل الفيديو...</p>
                        </div>
                        `;
                    if (playerSection) playerSection.appendChild(loadingDiv);
                }

                function hideVideoLoading() {
                    const loadingDiv = document.getElementById('video-loading');
                    if (loadingDiv) loadingDiv.remove();
                }

                // ===== Enhanced Video Events =====
                if (watchVideo) {
                    watchVideo.addEventListener('loadstart', () => {
                        console.log('Video loading started');
                        showVideoLoading();
                    });
                    watchVideo.addEventListener('loadeddata', () => {
                        console.log('Video data loaded');
                        hideVideoLoading();
                        setTimeout(() => applyAutoQuality(), 1000);
                    });
                    watchVideo.addEventListener('waiting', () => showVideoLoading());
                    watchVideo.addEventListener('canplay', () => hideVideoLoading());
                    watchVideo.addEventListener('error', (e) => {
                        console.error('Video error:', e);
                        hideVideoLoading();
                        showNotification('خطأ في تحميل الفيديو', 'error');
                    });
                }

                // ===== Progress Tracking =====
                let progressUpdateInterval = null;

                function startProgressTracking() {
                    if (progressUpdateInterval) clearInterval(progressUpdateInterval);
                    progressUpdateInterval = setInterval(() => {
                        if (watchVideo && watchVideo.currentTime > 0 && watchVideo.duration > 0) {
                            const progress = {
                                movieId: document.querySelector('[data-movie-id]')?.dataset.movieId,
                                currentTime: watchVideo.currentTime,
                                duration: watchVideo.duration,
                                timestamp: Date.now()
                            };
                            localStorage.setItem(`movie_progress_${progress.movieId}`, JSON.stringify(
                            progress));
                        }
                    }, 30000);
                }

                function stopProgressTracking() {
                    if (progressUpdateInterval) {
                        clearInterval(progressUpdateInterval);
                        progressUpdateInterval = null;
                    }
                }

                function loadSavedProgress() {
                    const movieId = document.querySelector('[data-movie-id]')?.dataset.movieId;
                    if (movieId && watchVideo) {
                        const savedProgress = localStorage.getItem(`movie_progress_${movieId}`);
                        if (savedProgress) {
                            try {
                                const progress = JSON.parse(savedProgress);
                                if (progress.currentTime > 60) {
                                    const resumeTime = Math.floor(progress.currentTime);
                                    const resumeDialog = document.createElement('div');
                                    resumeDialog.className =
                                        'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                                    resumeDialog.innerHTML = `
                                        <div class="bg-gray-800 p-6 rounded-lg text-white max-w-md">
                                            <h3 class="text-lg font-bold mb-4">استكمال المشاهدة</h3>
                                            <p class="mb-4">تم العثور على نقطة توقف سابقة عند ${formatTime(resumeTime)}. هل تريد الاستكمال من هناك؟</p>
                                            <div class="flex gap-3 justify-end">
                                                <button id="resumeNo" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-700">من البداية</button>
                                                <button id="resumeYes" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700">استكمال</button>
                                            </div>
                                        </div>
                                    `;
                                    document.body.appendChild(resumeDialog);
                                    document.getElementById('resumeYes').addEventListener('click', () => {
                                        watchVideo.currentTime = progress.currentTime;
                                        document.body.removeChild(resumeDialog);
                                    });
                                    document.getElementById('resumeNo').addEventListener('click', () => {
                                        document.body.removeChild(resumeDialog);
                                    });
                                }
                            } catch (e) {
                                console.error('Error loading progress:', e);
                            }
                        }
                    }
                }

                if (watchVideo) {
                    watchVideo.addEventListener('play', startProgressTracking);
                    watchVideo.addEventListener('pause', stopProgressTracking);
                    watchVideo.addEventListener('ended', stopProgressTracking);
                    watchVideo.addEventListener('loadedmetadata', loadSavedProgress);
                }

                // ===== Mobile Touch Controls =====
                let touchStartX = 0;
                let isScrubbing = false;

                if (watchVideo) {
                    watchVideo.addEventListener('touchstart', (e) => {
                        touchStartX = e.touches[0].clientX;
                    });

                    watchVideo.addEventListener('touchmove', (e) => {
                        if (!isScrubbing) {
                            const touchX = e.touches[0].clientX;
                            const diffX = touchX - touchStartX;
                            if (Math.abs(diffX) > 50) {
                                isScrubbing = true;
                                const seekAmount = diffX > 0 ? 10 : -10;
                                watchVideo.currentTime = Math.max(0, Math.min(watchVideo.duration, watchVideo
                                    .currentTime + seekAmount));
                            }
                        }
                    });

                    watchVideo.addEventListener('touchend', () => {
                        isScrubbing = false;
                    });

                    let lastTap = 0;
                    watchVideo.addEventListener('touchend', (e) => {
                        const currentTime = new Date().getTime();
                        const tapLength = currentTime - lastTap;
                        if (tapLength < 500 && tapLength > 0) {
                            if (playBtn) playBtn.click();
                        }
                        lastTap = currentTime;
                    });
                }

                // ===== Cleanup =====
                window.addEventListener('beforeunload', () => {
                    stopProgressTracking();
                    if (heroVideoTimer) clearTimeout(heroVideoTimer);
                    if (heroVideo) {
                        heroVideo.pause();
                        heroVideo.currentTime = 0;
                    }
                    if (watchVideo) watchVideo.pause();
                });

                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        if (heroVideo && !heroVideo.paused) heroVideo.pause();
                        if (watchVideo && !watchVideo.paused) stopProgressTracking();
                    } else {
                        if (watchVideo && !watchVideo.paused) startProgressTracking();
                    }
                });

                // ===== Initialize All Components =====
                console.log('Initializing all components');
                initHeroSection();
                initWatchNowButtons();
                initVideoControls();
                initWatchlistButton();
                initShareButton();
                initTabs();
                initCommentModal();
                initSwiper();
                console.log('All components initialized successfully');
            });
        </script>
    @endpush

</x-front-layout>
