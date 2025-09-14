<x-front-layout>
    @php
        $title = 'title_' . app()->getLocale();
        $description = 'description_' . app()->getLocale();

        // متغيرات التحكم في الوصول
        $requireLogin = $movie->require_login ?? false;
        $requireSubscription = $movie->require_subscription ?? false;
        $isAuthenticated = auth()->check();
        $hasSubscription = $isAuthenticated ? auth()->user()->has_active_subscription ?? true : false;

        // جلب الأفلام ذات الصلة حسب التصنيفات
        $relatedMovies = \App\Models\Movie::published()
            ->where('id', '!=', $movie->id)
            ->whereHas('categories', function ($q) use ($movie) {
                $q->whereIn('movie_categories.id', $movie->categories->pluck('id'));
            })
            ->limit(10)
            ->get();

        // جلب الأفلام الأعلى مشاهدة
        $topViewedMovies = \App\Models\Movie::published()
            ->where('id', '!=', $movie->id)
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();

        // جلب التعليقات
        $comments = $movie
            ->comments()
            ->approved()
            ->topLevel()
            ->with(['user', 'profile'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // جلب الممثلين والمخرجين
        $actors = $movie->cast()->person()->actors()->ordered()->get();
        $directors = $movie->cast()->person()->directors()->ordered()->get();
        $allCast = $movie->cast()->person()->ordered()->get();
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

            .movie-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .movie-card:hover {
                transform: scale(1.05);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            }
        </style>
        <style>
            .subtitle-text {
                position: absolute;
                bottom: 100px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 8px 16px;
                border-radius: 6px;
                text-align: center;
                font-size: 18px;
                font-weight: 500;
                max-width: 90%;
                line-height: 1.4;
                z-index: 25;
                word-wrap: break-word;
                white-space: pre-wrap;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            /* تحسين عرض الترجمات على الشاشات الصغيرة */
            @media (max-width: 768px) {
                .subtitle-text {
                    font-size: 16px;
                    bottom: 80px;
                    max-width: 95%;
                    padding: 6px 12px;
                }
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
                        <div class="text-sm text-gray-300">{{ $movie->duration_formatted }}</div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        @if ($watchProgress && $watchProgress->progress_percentage > 5)
                            <!-- زر متابعة المشاهدة -->
                            <button id="continueWatching" data-progress="{{ $watchProgress->watched_seconds }}"
                                class="flex items-center px-6 py-2 text-sm font-bold text-white bg-green-600 rounded-lg transition-all hover:bg-green-700">
                                <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                                متابعة المشاهدة ({{ number_format($watchProgress->progress_percentage, 1) }}%)
                            </button>

                            <!-- زر من البداية -->
                            <button id="watchFromStart"
                                class="flex items-center px-4 py-2 text-sm text-white bg-gray-700 rounded-lg hover:bg-gray-600">
                                <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z" />
                                </svg>
                                من البداية
                            </button>
                        @else
                            <!-- زر شاهد الآن العادي -->
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
                        @endif

                        <button id="addToWatchlist" data-movie-id="{{ $movie->id }}"
                            class="flex gap-2 items-center px-5 py-2 text-sm font-bold text-white bg-gray-700 rounded-lg transition-all hover:bg-gray-600">
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
    </section>

    <!-- Video Player -->
    <section id="playerSection" class="hidden fixed inset-0 z-[9999] bg-black">
        <div class="relative w-full h-full">
            <video id="watchVideo" class="object-cover z-10 w-full h-full" playsinline
                data-intro="{{ $movie->intro_skip_time ?? 0 }}"></video>
            <div id="subtitleText" class="hidden subtitle-text"></div>

            @if ($movie->intro_skip_time && $movie->intro_skip_time > 0)
                <button id="skipIntroBtn"
                    class="hidden absolute right-5 bottom-16 z-30 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded transition hover:bg-red-700">
                    ↩ {{ __('site.skip_intro') }}
                </button>
            @endif

            @if ($movie->videoFiles && $movie->videoFiles->count() > 0)
                <div class="pointer-events-auto quality-selector">
                    <div class="relative">
                        <button id="qualityBtn" class="p-2 text-sm text-white rounded bg-black/50 hover:bg-black/70">
                            <i class="mr-1 fas fa-cog"></i>
                            <span id="currentQuality">{{ __('site.auto') }}</span>
                            <i class="ml-1 fas fa-chevron-up"></i>
                        </button>
                        <div id="qualityDropdown" class="hidden absolute right-0 bottom-full mb-2 quality-dropdown">
                            <div class="quality-option active" data-quality="auto">{{ __('site.auto') }}</div>
                            @foreach ($movie->videoFiles->sortBy('quality') as $videoFile)
                                <div class="quality-option" data-quality="{{ $videoFile->quality }}"
                                    data-url="{{ $videoFile->file_url }}">
                                    {{ $videoFile->quality }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($movie->subtitles && $movie->subtitles->count() > 0)
                <div class="pointer-events-auto subtitle-controls">
                    <div class="relative">
                        <button id="subtitleBtn" class="p-2 text-sm text-white rounded bg-black/50 hover:bg-black/70">
                            <i class="mr-1 fas fa-closed-captioning"></i>
                            <span id="currentSubtitle">{{ __('site.off') }}</span>
                        </button>
                        <div id="subtitleDropdown" class="hidden absolute right-0 bottom-full mb-2 quality-dropdown">
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
                            @foreach ($relatedMovies as $relatedMovie)
                                <div class="swiper-slide">
                                    <div class="movie-slider-card">
                                        <img src="{{ $relatedMovie->poster_full_url }}"
                                            alt="{{ $relatedMovie->$title }}"
                                            class="object-cover w-full rounded-md aspect-video">
                                        <div class="movie-slider-details">
                                            <h3 class="text-lg font-bold">{{ $relatedMovie->$title }}</h3>
                                            <div class="movie-slider-line">
                                                <span>{{ $relatedMovie->duration_formatted }}</span>
                                                <span class="text-green-400">•</span>
                                                <span>{{ $relatedMovie->categories->pluck(app()->getLocale() == 'ar' ? 'name_ar' : 'name_en')->implode(', ') }}</span>
                                            </div>
                                            <div
                                                class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                                {{ Str::limit($relatedMovie->$description, 50) }}</div>
                                            <div
                                                class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                                <button
                                                    class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </button>
                                                <a href="{{ route('movie.show', $relatedMovie) }}"
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
            <!-- Related Movies Tab -->
            <div id="episodes" class="tab-content active animate-fade-in">
                @if ($relatedMovies->count() > 0)
                    <div class="overflow-visible px-4 py-6 mx-auto mb-3">
                        <h2 class="mb-4 text-2xl font-bold text-right">أفلام ذات صلة</h2>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                            @foreach ($relatedMovies as $relatedMovie)
                                <div class="overflow-hidden bg-gray-800 rounded-lg movie-card">
                                    <a href="{{ route('movie.show', $relatedMovie) }}">
                                        <img src="{{ $relatedMovie->poster_full_url }}"
                                            alt="{{ $relatedMovie->$title }}"
                                            class="w-full aspect-[2/3] object-cover">
                                        <div class="p-3">
                                            <h3 class="mb-1 text-sm font-bold text-white line-clamp-2">
                                                {{ $relatedMovie->$title }}</h3>
                                            <div class="mb-2 text-xs text-gray-400">
                                                <span>{{ $relatedMovie->release_date?->format('Y') }}</span>
                                                <span class="mx-1">•</span>
                                                <span>{{ $relatedMovie->duration_formatted }}</span>
                                            </div>
                                            <div class="text-xs text-yellow-400">
                                                ⭐ {{ $relatedMovie->imdb_rating }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($topViewedMovies->count() > 0)
                    <div class="overflow-visible px-4 py-6 mx-auto mb-3">
                        <h2 class="mb-4 text-2xl font-bold text-right">أعلى المشاهدة</h2>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                            @foreach ($topViewedMovies as $topMovie)
                                <div class="overflow-hidden bg-gray-800 rounded-lg movie-card">
                                    <a href="{{ route('movie.show', $topMovie) }}">
                                        <img src="{{ $topMovie->poster_full_url }}" alt="{{ $topMovie->$title }}"
                                            class="w-full aspect-[2/3] object-cover">
                                        <div class="p-3">
                                            <h3 class="mb-1 text-sm font-bold text-white line-clamp-2">
                                                {{ $topMovie->$title }}</h3>
                                            <div class="mb-2 text-xs text-gray-400">
                                                <span>{{ $topMovie->release_date?->format('Y') }}</span>
                                                <span class="mx-1">•</span>
                                                <span>{{ $topMovie->duration_formatted }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <div class="text-xs text-yellow-400">
                                                    ⭐ {{ $topMovie->imdb_rating }}
                                                </div>
                                                <div class="text-xs text-green-400">
                                                    👁 {{ number_format($topMovie->view_count) }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Details Tab -->
            <div id="details" class="tab-content animate-fade-in">
                <h2 class="pb-2 mb-6 text-2xl font-bold text-white border-b border-gray-600">تفاصيل الفيلم</h2>
                <div class="grid grid-cols-1 gap-6 text-gray-300 md:grid-cols-2">
                    <div class="col-span-2">
                        <p class="text-lg leading-relaxed">{{ $movie->$description }}</p>
                    </div>

                    @if ($movie->categories->count() > 0)
                        <div class="flex gap-3 items-center">
                            <i class="text-sky-400 fas fa-film"></i>
                            <span>
                                <span class="font-semibold text-white">التصنيف:</span>
                                {{ $movie->categories->pluck(app()->getLocale() == 'ar' ? 'name_ar' : 'name_en')->implode('، ') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex gap-3 items-center">
                        <i class="text-yellow-400 fas fa-clock"></i>
                        <span><span class="font-semibold text-white">المدة:</span>
                            {{ $movie->duration_formatted }}</span>
                    </div>

                    @if ($movie->release_date)
                        <div class="flex gap-3 items-center">
                            <i class="text-green-400 fas fa-calendar-alt"></i>
                            <span><span class="font-semibold text-white">سنة الإنتاج:</span>
                                {{ $movie->release_date->format('Y') }}</span>
                        </div>
                    @endif

                    @if ($movie->language)
                        <div class="flex gap-3 items-center">
                            <i class="text-purple-400 fas fa-language"></i>
                            <span><span class="font-semibold text-white">اللغة:</span> {{ $movie->language }}</span>
                        </div>
                    @endif

                    @if ($movie->country)
                        <div class="flex gap-3 items-center">
                            <i class="text-red-400 fas fa-globe"></i>
                            <span><span class="font-semibold text-white">البلد:</span> {{ $movie->country }}</span>
                        </div>
                    @endif

                    @if ($movie->imdb_rating)
                        <div class="flex gap-3 items-center">
                            <i class="text-yellow-400 fas fa-star"></i>
                            <span><span class="font-semibold text-white">تقييم IMDb:</span>
                                {{ $movie->imdb_rating }}/10</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cast Tab -->
            <div id="cast" class="tab-content animate-fade-in">
                <h2 class="pb-2 mb-6 text-2xl font-bold text-white border-b border-gray-600">الممثلين والطاقم</h2>

                @if ($directors->count() > 0)
                    <div class="mb-8">
                        <h3 class="mb-4 text-xl font-semibold text-white">المخرجون</h3>
                        <div class="grid grid-cols-2 gap-6 text-center sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                            @foreach ($directors as $director)
                                <div class="transition-transform duration-300 group hover:scale-105">
                                    <div class="overflow-hidden rounded-lg shadow-md">
                                        <img src="{{ $director->person->photo_full_url ?: 'https://via.placeholder.com/150x200?text=No+Image' }}"
                                            alt="{{ $director->person->name }}"
                                            class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                                    </div>
                                    <span
                                        class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">
                                        {{ $director->person->name }}
                                    </span>
                                    <span class="block text-xs text-gray-500">مخرج</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($actors->count() > 0)
                    <div class="mb-8">
                        <h3 class="mb-4 text-xl font-semibold text-white">الممثلون</h3>
                        <div class="grid grid-cols-2 gap-6 text-center sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                            @foreach ($actors as $actor)
                                <div class="transition-transform duration-300 group hover:scale-105">
                                    <div class="overflow-hidden rounded-lg shadow-md">
                                        <img src="{{ $actor->person->photo_full_url ?: 'https://via.placeholder.com/150x200?text=No+Image' }}"
                                            alt="{{ $actor->person->name }}"
                                            class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                                    </div>
                                    <a href="{{ route('site.actor', $actor->person->id) }}"
                                        class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">
                                        {{ $actor->person->name }}
                                    </a>
                                    @if ($actor->character_name)
                                        <span class="block text-xs text-gray-500">{{ $actor->character_name }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Comments Tab -->
            <div id="comments" class="tab-content animate-fade-in">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">التعليقات ({{ $comments->count() }})</h2>
                    @auth
                        <button id="openCommentModal"
                            class="px-4 py-1 text-sm text-white rounded transition-all bg-fire-red hover:bg-red-700">+ أضف
                            تعليق</button>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-1 text-sm text-white bg-gray-600 rounded transition-all hover:bg-gray-700">سجل
                            دخولك لإضافة تعليق</a>
                    @endauth
                </div>

                <div id="commentsList" class="space-y-4">
                    @forelse($comments as $comment)
                        <div class="flex items-start p-4 bg-gray-800 bg-opacity-40 rounded-lg shadow-sm">
                            <img src="{{ $comment->user->avatar ?? ($comment->profile?->avatar ?? './assets/images/avatar.jpg') }}"
                                class="ml-3 w-10 h-10 rounded-full" alt="Avatar">
                            <div class="flex-1">
                                <div class="flex gap-2 items-center mb-1">
                                    <p class="font-bold text-white">
                                        {{ $comment->profile?->display_name ?? ($comment->user->name ?? 'مستخدم') }}
                                    </p>
                                    <span class="text-xs text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    @if ($comment->is_edited)
                                        <span class="text-xs text-gray-500">(معدل)</span>
                                    @endif
                                </div>
                                <p class="text-sm leading-relaxed text-gray-300">{{ $comment->content }}</p>

                                @if ($comment->likes_count > 0)
                                    <div class="flex gap-2 items-center mt-2">
                                        <button class="text-xs text-gray-400 transition-colors hover:text-red-400">
                                            ❤️ {{ $comment->likes_count }}
                                        </button>
                                    </div>
                                @endif

                                @if ($comment->replies_count > 0)
                                    <button class="mt-2 text-xs text-sky-400 transition-colors hover:text-sky-300">
                                        عرض {{ $comment->replies_count }} رد
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <div class="mb-2 text-lg text-gray-400">📝</div>
                            <p class="text-gray-400">لا توجد تعليقات بعد. كن أول من يعلق!</p>
                        </div>
                    @endforelse
                </div>

                @if ($comments->count() >= 20)
                    <div class="mt-6 text-center">
                        <button id="loadMoreComments"
                            class="px-6 py-2 text-sm text-white bg-gray-700 rounded transition-all hover:bg-gray-600">
                            عرض المزيد من التعليقات
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Comment Modal -->
    @auth
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
    @endauth

    @php
        $videoFiles = $movie->videoFiles
            ->map(function ($file) {
                return [
                    'quality' => $file->quality,
                    'url' => $file->file_url,
                    'size' => $file->file_size ?? 0,
                ];
            })
            ->sortBy('quality')
            ->values();
    @endphp
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
        <script>
            // في بداية DOMContentLoaded بعد تعريف المتغيرات
            const activeProfileId = localStorage.getItem('active_profile_id');
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            const watchProgress = @json($watchProgress ?? null);

            console.log('Auth Status:', isAuthenticated);
            console.log('Active Profile ID:', activeProfileId);
            console.log('Watch Progress:', watchProgress);
            const SEEK_TIME = 10; // يمكنك تغيير هذا الرقم (بالثواني)
            const VOLUME_STEP = 0.1; // خطوة تغيير الصوت

            // فحص صحة حالة المصادقة والبروفايل
            if (activeProfileId && !isAuthenticated) {
                console.log('Profile exists but not authenticated, clearing profile');
                localStorage.removeItem('active_profile_id');
                activeProfileId = null;
            }
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM Loaded - Starting initialization');

                // ===== Video Files Data =====
                const videoFiles = @json($videoFiles);

                console.log('Available video files:', videoFiles);

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

                        // إضافة مستمع للانتهاء من التريلر
                        heroVideo.addEventListener('ended', () => {
                            console.log('Trailer ended, returning to image');
                            stopTrailer();
                        });

                        // إضافة مستمع للأخطاء
                        heroVideo.addEventListener('error', (e) => {
                            console.error('Trailer error:', e);
                            stopTrailer();
                        });

                        if (muteBtn) muteBtn.addEventListener('click', toggleTrailerSound);
                    } else {
                        console.log('No trailer found');
                        if (muteBtn) muteBtn.style.display = 'none';
                    }
                }

                function playTrailer() {
                    if (heroVideo && heroImage && heroVideo.src) {
                        console.log('Playing trailer');
                        heroImage.style.opacity = '0';

                        setTimeout(() => {
                            heroImage.classList.add('hidden');
                            heroVideo.classList.remove('hidden');
                            heroVideo.style.opacity = '1';

                            // تأكد من إعداد الفيديو بشكل صحيح
                            heroVideo.muted = true;
                            heroVideo.loop = false; // إزالة التكرار
                            heroVideo.currentTime = 0; // البدء من الصفر

                            heroVideo.play().then(() => {
                                isTrailerPlaying = true;
                                console.log('Trailer started successfully');
                            }).catch(e => {
                                console.error('Failed to play trailer:', e);
                                stopTrailer();
                            });
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
                            heroVideo.currentTime = 0; // إعادة تعيين الوقت للبداية
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

                    // زر متابعة المشاهدة
                    const continueBtn = document.getElementById('continueWatching');
                    if (continueBtn) {
                        continueBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            const progressTime = parseFloat(continueBtn.dataset.progress);
                            startVideoPlayer(progressTime);
                        });
                    }

                    // زر من البداية
                    const watchFromStartBtn = document.getElementById('watchFromStart');
                    if (watchFromStartBtn) {
                        watchFromStartBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            startVideoPlayer(0);
                        });
                    }

                    // الأزرار العادية
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

                    // تمرير 0 كقيمة افتراضية
                    startVideoPlayer(0);
                }

                function startVideoPlayer(startTime) {
                    // إضافة قيمة افتراضية للـ startTime
                    startTime = startTime || 0;

                    console.log('Starting video player at time:', startTime);
                    if (playerSection && watchVideo) {
                        if (heroVideoTimer) clearTimeout(heroVideoTimer);
                        if (isTrailerPlaying) stopTrailer();

                        const optimalVideo = getOptimalVideoQuality();
                        if (optimalVideo && optimalVideo.url) {
                            console.log('Setting optimal video:', optimalVideo);
                            watchVideo.src = optimalVideo.url;
                        } else if (videoFiles && videoFiles.length > 0) {
                            console.log('Using first available video:', videoFiles[0]);
                            watchVideo.src = videoFiles[0].url;
                        }

                        playerSection.classList.remove('hidden');
                        watchVideo.classList.remove('hidden');
                        watchVideo.volume = 1;
                        watchVideo.muted = false;
                        requestFullscreen(playerSection);

                        // تعيين الوقت بعد تحميل البيانات
                        if (startTime > 0) {
                            const setStartTime = () => {
                                watchVideo.currentTime = startTime;
                                watchVideo.removeEventListener('loadeddata', setStartTime);
                            };
                            watchVideo.addEventListener('loadeddata', setStartTime);
                        } else {
                            watchVideo.currentTime = 0;
                        }

                        setTimeout(() => {
                            watchVideo.play().catch(e => console.log('Video play error:', e));
                        }, 100);
                    }
                }

                function requestFullscreen(element) {
                    if (element.requestFullscreen) element.requestFullscreen();
                    else if (element.webkitRequestFullscreen) element.webkitRequestFullscreen();
                    else if (element.msRequestFullscreen) element.msRequestFullscreen();
                }

                // ===== Video Quality Management =====
                function getOptimalVideoQuality() {
                    if (!videoFiles || videoFiles.length === 0) return null;

                    // Auto quality detection based on network and device
                    const optimalQuality = detectOptimalQuality();
                    console.log('Detected optimal quality:', optimalQuality);

                    // Find the best matching quality
                    let selectedVideo = videoFiles.find(video => video.quality === optimalQuality);

                    if (!selectedVideo) {
                        // Fallback: find closest lower quality
                        const qualityOrder = ['240p', '360p', '480p', '720p', '1080p', '4K'];
                        const targetIndex = qualityOrder.indexOf(optimalQuality);

                        for (let i = targetIndex; i >= 0; i--) {
                            selectedVideo = videoFiles.find(video => video.quality === qualityOrder[i]);
                            if (selectedVideo) break;
                        }

                        // If still not found, use first available
                        if (!selectedVideo) {
                            selectedVideo = videoFiles[0];
                        }
                    }

                    return selectedVideo;
                }

                function detectOptimalQuality() {
                    // Check network connection
                    if (navigator.connection) {
                        const connection = navigator.connection;
                        const effectiveType = connection.effectiveType;
                        const downlink = connection.downlink; // Mbps

                        console.log('Network info:', {
                            effectiveType,
                            downlink
                        });

                        if (downlink) {
                            if (downlink >= 10) return '1080p';
                            if (downlink >= 5) return '720p';
                            if (downlink >= 1.5) return '480p';
                            return '360p';
                        }

                        switch (effectiveType) {
                            case 'slow-2g':
                            case '2g':
                                return '240p';
                            case '3g':
                                return '360p';
                            case '4g':
                                return '720p';
                            default:
                                return '480p';
                        }
                    }

                    // Fallback: detect by screen size
                    const screenWidth = window.screen.width;
                    if (screenWidth >= 1920) return '1080p';
                    if (screenWidth >= 1280) return '720p';
                    if (screenWidth >= 854) return '480p';
                    return '360p';
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
                    if (rewindBtn) {
                        rewindBtn.addEventListener('click', () => {
                            seekVideo(-SEEK_TIME);
                        });
                    }

                    if (forwardBtn) {
                        forwardBtn.addEventListener('click', () => {
                            seekVideo(SEEK_TIME);
                        });
                    }
                    if (muteVideoBtn) {
                        muteVideoBtn.addEventListener('click', () => {
                            watchVideo.muted = !watchVideo.muted;
                            muteVideoBtn.innerHTML = watchVideo.muted ? '<i class="fas fa-volume-mute"></i>' :
                                '<i class="fas fa-volume-up"></i>';
                        });
                    }
                    if (exitBtn) exitBtn.addEventListener('click', () => exitFullscreen());
                    if (progressBar) {
                        // إزالة المستمع القديم وإضافة مستمعين جدد
                        progressBar.addEventListener('mousedown', startScrubbing);
                        progressBar.addEventListener('input', handleProgressChange);
                        progressBar.addEventListener('change', handleProgressChange);

                        // دعم اللمس للموبايل
                        progressBar.addEventListener('touchstart', startScrubbing);
                    }
                    if (watchVideo) {
                        watchVideo.addEventListener('timeupdate', updateProgress);
                        watchVideo.addEventListener('loadedmetadata', updateDuration);
                        watchVideo.addEventListener('ended', onVideoEnded);
                        watchVideo.addEventListener('loadstart', () => showVideoLoading());
                        watchVideo.addEventListener('canplay', () => hideVideoLoading());
                        watchVideo.addEventListener('waiting', () => showVideoLoading());
                        watchVideo.addEventListener('error', (e) => {
                            console.error('Video error:', e);
                            hideVideoLoading();
                            showNotification('خطأ في تحميل الفيديو', 'error');
                        });
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

                function seekVideo(seconds) {
                    if (!watchVideo || !watchVideo.duration) return;

                    const currentTime = watchVideo.currentTime;
                    const newTime = Math.max(0, Math.min(watchVideo.duration, currentTime + seconds));

                    console.log(`Seeking from ${currentTime.toFixed(2)}s to ${newTime.toFixed(2)}s`);

                    watchVideo.currentTime = newTime;
                    showSeekFeedback(seconds);
                }
                function showSeekFeedback(seconds) {
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute inset-0 flex items-center justify-center z-40 pointer-events-none';
                    const direction = seconds > 0 ? 'forward' : 'backward';
                    const time = Math.abs(seconds);

                    indicator.innerHTML = `
                        <div class="flex gap-3 items-center p-4 bg-black bg-opacity-80 rounded-lg">
                            <i class="fas fa-${direction === 'forward' ? 'forward' : 'backward'} text-white text-2xl"></i>
                            <span class="text-lg font-semibold text-white">${time}s</span>
                        </div>
                    `;

                    playerSection.appendChild(indicator);
                    setTimeout(() => indicator.remove(), 1000);
                }
                let isScrubbing = false;
                let wasPlaying = false;

                function startScrubbing(e) {
                    if (!watchVideo || !watchVideo.duration) return;

                    isScrubbing = true;
                    wasPlaying = !watchVideo.paused;
                    scrubbingStartTime = parseFloat(e.target.value);

                    // إيقاف الفيديو أثناء السحب
                    if (wasPlaying) {
                        watchVideo.pause();
                    }

                    console.log('Started scrubbing at:', scrubbingStartTime);
                }
                function handleProgressChange(e) {
                    if (!watchVideo || !watchVideo.duration) return;

                    const newTime = parseFloat(e.target.value);
                    console.log(`Progress changing to: ${newTime.toFixed(2)}s`);

                    // تحديث الوقت فوراً أثناء السحب
                    if (isScrubbing) {
                        watchVideo.currentTime = newTime;
                        if (currentTimeLabel) {
                            currentTimeLabel.textContent = formatTime(newTime);
                        }
                    }
                }
                // إضافة مستمعين للماوس والمس عند انتهاء السحب
                document.addEventListener('mouseup', endScrubbing);
                document.addEventListener('mouseleave', endScrubbing);
                document.addEventListener('touchend', endScrubbing);
                document.addEventListener('touchcancel', endScrubbing);

                function endScrubbing() {
                    if (!isScrubbing) return;

                    const finalTime = progressBar.value;
                    console.log('Ending scrubbing at:', finalTime, 'was playing:', wasPlaying);

                    // تأكد من تعيين الوقت النهائي
                    watchVideo.currentTime = parseFloat(finalTime);

                    isScrubbing = false;

                    // استئناف التشغيل بعد تأخير قصير
                    if (wasPlaying) {
                        setTimeout(() => {
                            watchVideo.play().catch(e => console.log('Resume play error:', e));
                        }, 100);
                    }
                }
                function updateProgress() {
                    // تجنب التحديث أثناء السحب
                    if (isScrubbing) {
                        console.log('Skipping progress update during scrubbing');
                        return;
                    }

                    if (progressBar && watchVideo.duration && !isNaN(watchVideo.currentTime)) {
                        progressBar.value = watchVideo.currentTime;
                    }
                    if (currentTimeLabel) {
                        currentTimeLabel.textContent = formatTime(watchVideo.currentTime);
                    }

                    // فحص وقت تخطي المقدمة
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
                    if (!sec || isNaN(sec)) return '0:00';
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

                // ===== Quality Selector =====
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
                                    changeVideoQuality(url);
                                } else if (quality === 'auto') {
                                    const optimalVideo = getOptimalVideoQuality();
                                    if (optimalVideo && optimalVideo.url) {
                                        changeVideoQuality(optimalVideo.url);
                                    }
                                }
                                qualityDropdown.classList.add('hidden');
                            }
                        });
                    }
                }

                function changeVideoQuality(newUrl) {
                    if (!watchVideo || watchVideo.src === newUrl) return;

                    const currentTime = watchVideo.currentTime;
                    const isPlaying = !watchVideo.paused;

                    console.log('Changing quality to:', newUrl);

                    showVideoLoading();
                    watchVideo.src = newUrl;

                    const handleLoadedMetadata = () => {
                        watchVideo.currentTime = currentTime;
                        if (isPlaying) {
                            watchVideo.play().then(() => {
                                hideVideoLoading();
                            }).catch(e => {
                                console.error('Failed to play after quality change:', e);
                                hideVideoLoading();
                            });
                        } else {
                            hideVideoLoading();
                        }
                        watchVideo.removeEventListener('loadedmetadata', handleLoadedMetadata);
                    };

                    watchVideo.addEventListener('loadedmetadata', handleLoadedMetadata);
                }

                // ===== Subtitle Selector =====
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
                                subtitleDropdown.querySelectorAll('.quality-option').forEach(opt => opt.classList.remove('active'));
                                e.target.classList.add('active');

                                if (subtitleLang === 'off') {
                                    disableSubtitles();
                                } else if (subtitleUrl) {
                                    await loadSubtitles(subtitleUrl, subtitleLang);
                                }
                                subtitleDropdown.classList.add('hidden');
                            }
                        });

                        // إضافة مستمع لتحديث الترجمات
                        if (watchVideo) {
                            watchVideo.addEventListener('timeupdate', updateSubtitles);
                        }
                    }
                }

                function disableSubtitles() {
                    const subtitleText = document.getElementById('subtitleText');
                    if (subtitleText) {
                        subtitleText.classList.add('hidden');
                        subtitleText.textContent = '';
                    }
                    currentSubtitleTrack = null;
                    subtitleData = [];
                    console.log('Subtitles disabled');
                }
                async function loadSubtitles(url, language) {
                    const subtitleText = document.getElementById('subtitleText');

                    try {
                        showNotification('جاري تحميل الترجمات...', 'info');

                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const srtContent = await response.text();
                        subtitleData = parseSRT(srtContent);
                        currentSubtitleTrack = language;

                        console.log(`Loaded ${subtitleData.length} subtitle entries for ${language}`);

                        if (subtitleText) {
                            subtitleText.classList.remove('hidden');
                        }

                        showNotification('تم تحميل الترجمات بنجاح', 'success');

                    } catch (error) {
                        console.error('Failed to load subtitles:', error);
                        showNotification('فشل في تحميل الترجمات', 'error');
                        disableSubtitles();
                    }
                }

                function updateSubtitles() {
                    const subtitleText = document.getElementById('subtitleText');

                    if (!currentSubtitleTrack || !subtitleData.length || !subtitleText || !watchVideo) {
                        return;
                    }

                    const currentTime = watchVideo.currentTime;

                    // البحث عن الترجمة المناسبة للوقت الحالي
                    const currentSub = subtitleData.find(sub =>
                        currentTime >= sub.start && currentTime <= sub.end
                    );

                    if (currentSub) {
                        if (subtitleText.textContent !== currentSub.text) {
                            subtitleText.textContent = currentSub.text;
                            subtitleText.classList.remove('hidden');
                            console.log(`Showing subtitle: ${currentSub.text}`);
                        }
                    } else {
                        if (!subtitleText.classList.contains('hidden')) {
                            subtitleText.classList.add('hidden');
                            subtitleText.textContent = '';
                        }
                    }
                }

                function parseSRT(srtText) {
                    const subtitles = [];

                    // تنظيف النص وتقسيمه
                    const blocks = srtText.trim().split(/\r?\n\s*\r?\n/);

                    blocks.forEach((block, index) => {
                        const lines = block.trim().split(/\r?\n/);

                        if (lines.length >= 3) {
                            const sequenceNumber = parseInt(lines[0]);
                            const timeRange = lines[1];
                            const text = lines.slice(2).join('\n').trim();

                            // تحليل الوقت
                            const timeMatch = timeRange.match(/(\d{2}):(\d{2}):(\d{2}),(\d{3})\s*-->\s*(\d{2}):(\d{2}):(\d{2}),(\d{3})/);

                            if (timeMatch && text) {
                                const startTime = timeToSeconds(timeMatch.slice(1, 5));
                                const endTime = timeToSeconds(timeMatch.slice(5, 9));

                                subtitles.push({
                                    id: sequenceNumber || index + 1,
                                    start: startTime,
                                    end: endTime,
                                    text: text,
                                    duration: endTime - startTime
                                });
                            } else {
                                console.warn(`Invalid subtitle block at index ${index}:`, block);
                            }
                        }
                    });

                    // ترتيب الترجمات حسب وقت البداية
                    subtitles.sort((a, b) => a.start - b.start);

                    console.log(`Parsed ${subtitles.length} subtitle entries`);
                    return subtitles;
                }

                function timeToSeconds(timeParts) {
                    // timeParts: [hours, minutes, seconds, milliseconds]
                    const hours = parseInt(timeParts[0]) || 0;
                    const minutes = parseInt(timeParts[1]) || 0;
                    const seconds = parseInt(timeParts[2]) || 0;
                    const milliseconds = parseInt(timeParts[3]) || 0;

                    return hours * 3600 + minutes * 60 + seconds + milliseconds / 1000;
                }

                // Hide dropdowns on outside click
                document.addEventListener('click', () => {
                    const qualityDropdown = document.getElementById('qualityDropdown');
                    const subtitleDropdown = document.getElementById('subtitleDropdown');
                    if (qualityDropdown) qualityDropdown.classList.add('hidden');
                    if (subtitleDropdown) subtitleDropdown.classList.add('hidden');
                });

                // ===== Loading States =====
                function showVideoLoading() {
                    const existingLoading = document.getElementById('video-loading');
                    if (existingLoading) return;

                    const loadingDiv = document.createElement('div');
                    loadingDiv.id = 'video-loading';
                    loadingDiv.className =
                        'absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30';
                    loadingDiv.innerHTML = `
                            <div class="text-center">
                                <div class="mx-auto mb-4 w-12 h-12 rounded-full border-b-2 border-white animate-spin"></div>
                                <p class="text-white">جاري تحميل الفيديو...</p>
                            </div>
                        `;
                    if (playerSection) playerSection.appendChild(loadingDiv);
                }

                function hideVideoLoading() {
                    const loadingDiv = document.getElementById('video-loading');
                    if (loadingDiv) loadingDiv.remove();
                }

                // ===== Watchlist Button =====
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

                    btn.disabled = true;
                    icon.className = 'fas fa-spinner fa-spin';

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
                                icon.classList.replace('fa-spinner', 'fa-check');
                                icon.classList.remove('fa-spin');
                                text.textContent = 'في القائمة';
                                btn.classList.add('bg-green-600', 'hover:bg-green-700');
                                btn.classList.remove('bg-gray-700', 'hover:bg-gray-600');
                            } else {
                                icon.classList.replace('fa-spinner', 'fa-plus');
                                icon.classList.remove('fa-spin');
                                text.textContent = 'أضف للمفضلة';
                                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                                btn.classList.add('bg-gray-700', 'hover:bg-gray-600');
                            }
                            showNotification(data.message || 'تم التحديث بنجاح', 'success');
                        } else {
                            icon.classList.replace('fa-spinner', 'fa-plus');
                            icon.classList.remove('fa-spin');
                            showNotification(data.message || 'حدث خطأ', 'error');
                        }
                    } catch (error) {
                        console.error('Watchlist error:', error);
                        icon.classList.replace('fa-spinner', 'fa-plus');
                        icon.classList.remove('fa-spin');
                        showNotification('فشل في تحديث قائمة المشاهدة', 'error');
                    } finally {
                        btn.disabled = false;
                    }
                }

                // ===== Share Button =====
                const copyLinkBtn = document.getElementById('copy-link');
                const copyAlert = document.getElementById('copy-alert');

                function initShareButton() {
                    if (copyLinkBtn) copyLinkBtn.addEventListener('click', handleShareClick);
                }

                async function handleShareClick(e) {
                    e.preventDefault();
                    const url = window.location.href;

                    if (navigator.share) {
                        try {
                            await navigator.share({
                                title: document.title,
                                url: url
                            });
                            return;
                        } catch (error) {
                            console.log('Native share failed, falling back to clipboard');
                        }
                    }

                    try {
                        await navigator.clipboard.writeText(url);
                        if (copyAlert) {
                            copyAlert.classList.add('opacity-100');
                            copyAlert.classList.remove('opacity-0');
                            setTimeout(() => {
                                copyAlert.classList.remove('opacity-100');
                                copyAlert.classList.add('opacity-0');
                            }, 2500);
                        }
                    } catch (err) {
                        console.error('فشل النسخ:', err);
                        showNotification('فشل في نسخ الرابط', 'error');
                    }
                }

                // ===== Tabs =====
                function initTabs() {
                    console.log('Initializing tabs');
                    const tabButtons = document.querySelectorAll('.tab');
                    const tabContents = document.querySelectorAll('.tab-content');

                    tabButtons.forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const target = this.getAttribute('data-tab');

                            tabButtons.forEach(b => {
                                b.classList.remove('bg-fire-red', 'active');
                            });
                            tabContents.forEach(c => {
                                c.classList.remove('active');
                                c.style.display = 'none';
                            });

                            this.classList.add('bg-fire-red', 'active');

                            const targetElement = document.getElementById(target);
                            if (targetElement) {
                                targetElement.style.display = 'block';
                                targetElement.classList.add('active');
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

                    if (commentInput) {
                        commentInput.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter' && e.ctrlKey) {
                                handleCommentSubmit();
                            }
                        });
                    }
                }

                async function handleCommentSubmit() {
                    const text = commentInput ? commentInput.value.trim() : '';
                    if (text === "") {
                        showNotification('يرجى كتابة تعليق', 'error');
                        return;
                    }

                    const originalText = submitComment.textContent;
                    submitComment.textContent = 'جاري النشر...';
                    submitComment.disabled = true;

                    try {
                        const response = await fetch(`/api/movies/{{ $movie->id }}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                content: text
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            const newComment = document.createElement("div");
                            newComment.className =
                                "flex items-start p-4 bg-gray-800 bg-opacity-40 rounded-lg shadow-sm animate-fade-in";
                            newComment.innerHTML = `
                    <img src="${data.comment.user_avatar || './assets/images/avatar.jpg'}" class="ml-3 w-10 h-10 rounded-full" alt="Avatar">
                    <div class="flex-1">
                        <div class="flex gap-2 items-center mb-1">
                            <p class="font-bold text-white">${data.comment.user_name}</p>
                            <span class="text-xs text-gray-400">الآن</span>
                        </div>
                        <p class="text-sm leading-relaxed text-gray-300">${text}</p>
                    </div>
                `;

                            const emptyMessage = commentsList.querySelector('.text-center');
                            if (emptyMessage) {
                                emptyMessage.remove();
                            }

                            if (commentsList) commentsList.prepend(newComment);
                            showNotification('تم إضافة التعليق بنجاح', 'success');
                        } else {
                            showNotification(data.message || 'فشل في إضافة التعليق', 'error');
                        }
                    } catch (error) {
                        console.error('Comment submission error:', error);
                        showNotification('فشل في إضافة التعليق', 'error');
                    } finally {
                        submitComment.textContent = originalText;
                        submitComment.disabled = false;

                        if (commentInput) commentInput.value = "";
                        if (modal) modal.classList.add("hidden");
                    }
                }

                // ===== Swiper Initialization =====
                function initSwiper() {
                    console.log('Initializing Swiper');

                    document.querySelectorAll('.mySwiper-horizontal').forEach(swiperElement => {
                        new Swiper(swiperElement, {
                            slidesPerView: 'auto',
                            spaceBetween: 20,
                            navigation: {
                                nextEl: swiperElement.querySelector('.swiper-button-next'),
                                prevEl: swiperElement.querySelector('.swiper-button-prev'),
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
                    });
                }

                // ===== Notification System =====
                function showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-[10000] px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full opacity-0 ${
                            type === 'success' ? 'bg-green-600' :
                            type === 'error' ? 'bg-red-600' : 'bg-blue-600'
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

                // ===== Progress Tracking =====
                let progressUpdateInterval = null;

                // حفظ تقدم المشاهدة كل 30 ثانية
                function startProgressTracking() {
                    if (progressUpdateInterval) clearInterval(progressUpdateInterval);
                        progressUpdateInterval = setInterval(() => {
                            if (watchVideo && watchVideo.currentTime > 0 && watchVideo.duration > 0) {
                                // حفظ محلي
                                const progress = {
                                    movieId: '{{ $movie->id }}',
                                    currentTime: watchVideo.currentTime,
                                    duration: watchVideo.duration,
                                    timestamp: Date.now()
                                };
                                localStorage.setItem(`movie_progress_${progress.movieId}`, JSON.stringify(progress));

                                // حفظ في قاعدة البيانات إذا كان مسجل
                                if (activeProfileId) {
                                    saveProgressToServer(watchVideo.currentTime, watchVideo.duration);
                                }
                            }
                    }, 30000);
                }

                function stopProgressTracking() {
                    if (progressUpdateInterval) {
                        clearInterval(progressUpdateInterval);
                        progressUpdateInterval = null;
                    }
                }
                // حفظ التقدم في السيرفر
                function saveProgressToServer(currentTime, duration) {
                    fetch(`/api/movies/{{ $movie->id }}/progress`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            current_time: currentTime,
                            duration: duration
                        })
                    }).catch(e => console.log('Failed to save progress:', e));
                }

                function loadSavedProgress() {
                    const movieId = '{{ $movie->id }}';
                    if (movieId && watchVideo) {
                        const savedProgress = localStorage.getItem(`movie_progress_${movieId}`);
                        if (savedProgress) {
                            try {
                                const progress = JSON.parse(savedProgress);
                                if (progress.currentTime > 60 && progress.currentTime < (progress.duration - 300)) {
                                    const resumeTime = Math.floor(progress.currentTime);
                                    const resumeDialog = document.createElement('div');
                                    resumeDialog.className =
                                        'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                                    resumeDialog.innerHTML = `
                            <div class="p-6 max-w-md text-white bg-gray-800 rounded-lg">
                                <h3 class="mb-4 text-lg font-bold">استكمال المشاهدة</h3>
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
                                        localStorage.removeItem(`movie_progress_${movieId}`);
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
                    watchVideo.addEventListener('ended', () => {
                        stopProgressTracking();
                        localStorage.removeItem(`movie_progress_{{ $movie->id }}`);
                    });
                    watchVideo.addEventListener('loadedmetadata', loadSavedProgress);
                }

                // ===== View Count Update =====
                function updateViewCount() {
                    fetch(`/api/movies/{{ $movie->id }}/view`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        }
                    }).catch(e => console.log('Failed to update view count:', e));
                }

                if (watchVideo) {
                    let viewCountUpdated = false;
                    watchVideo.addEventListener('timeupdate', () => {
                        if (!viewCountUpdated && watchVideo.currentTime > 30) {
                            updateViewCount();
                            viewCountUpdated = true;
                        }
                    });
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
                            case 'ArrowUp':
                                e.preventDefault();
                                if (watchVideo) watchVideo.volume = Math.min(1, watchVideo.volume + 0.1);
                                break;
                            case 'ArrowDown':
                                e.preventDefault();
                                if (watchVideo) watchVideo.volume = Math.max(0, watchVideo.volume - 0.1);
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
                            case 'j':
                            case 'J':
                                e.preventDefault();
                                if (watchVideo) watchVideo.currentTime -= 10;
                                break;
                            case 'l':
                            case 'L':
                                e.preventDefault();
                                if (watchVideo) watchVideo.currentTime += 10;
                                break;
                            case 'k':
                            case 'K':
                                e.preventDefault();
                                if (playBtn) playBtn.click();
                                break;
                        }
                    }
                });

                // ===== Mobile Touch Controls =====
                let touchStartX = 0;
                let touchStartY = 0;
                let isVolumeAdjusting = false;

                if (watchVideo) {
                    watchVideo.addEventListener('touchstart', (e) => {
                        touchStartX = e.touches[0].clientX;
                        touchStartY = e.touches[0].clientY;
                    });

                    watchVideo.addEventListener('touchmove', (e) => {
                        e.preventDefault();
                        const touchX = e.touches[0].clientX;
                        const touchY = e.touches[0].clientY;
                        const diffX = touchX - touchStartX;
                        const diffY = touchY - touchStartY;

                        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 30 && !isScrubbing) {
                            isScrubbing = true;
                            const seekAmount = (diffX / window.innerWidth) * 10;
                            const newTime = Math.max(0, Math.min(watchVideo.duration, watchVideo.currentTime +
                                seekAmount));
                            watchVideo.currentTime = newTime;
                            showSeekIndicator(seekAmount > 0 ? 'forward' : 'backward', Math.abs(seekAmount));
                        }

                        if (Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 30 && !isVolumeAdjusting) {
                            isVolumeAdjusting = true;
                            const isRightSide = touchStartX > window.innerWidth / 2;

                            if (isRightSide) {
                                const volumeChange = -(diffY / window.innerHeight);
                                const newVolume = Math.max(0, Math.min(1, watchVideo.volume + volumeChange));
                                watchVideo.volume = newVolume;
                                showVolumeIndicator(newVolume);
                            }
                        }
                    });

                    watchVideo.addEventListener('touchend', () => {
                        isScrubbing = false;
                        isVolumeAdjusting = false;
                    });

                    let lastTap = 0;
                    watchVideo.addEventListener('touchend', (e) => {
                        const currentTime = new Date().getTime();
                        const tapLength = currentTime - lastTap;
                        if (tapLength < 500 && tapLength > 0) {
                            e.preventDefault();
                            if (playBtn) playBtn.click();
                        }
                        lastTap = currentTime;
                    });
                }

                function showSeekIndicator(direction, amount) {
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute inset-0 flex items-center justify-center z-40 pointer-events-none';
                    indicator.innerHTML = `
                            <div class="p-4 bg-black bg-opacity-70 rounded-full">
                                <i class="fas fa-${direction === 'forward' ? 'forward' : 'backward'} text-white text-2xl"></i>
                                <p class="mt-2 text-sm text-white">${Math.round(amount)}s</p>
                            </div>
                        `;
                    playerSection.appendChild(indicator);
                    setTimeout(() => indicator.remove(), 1000);
                }

                function showVolumeIndicator(volume) {
                    let indicator = document.getElementById('volume-indicator');
                    if (!indicator) {
                        indicator = document.createElement('div');
                        indicator.id = 'volume-indicator';
                        indicator.className =
                            'absolute top-1/2 right-4 transform -translate-y-1/2 z-40 pointer-events-none';
                        playerSection.appendChild(indicator);
                    }

                    const percentage = Math.round(volume * 100);
                    indicator.innerHTML = `
                            <div class="flex flex-col items-center p-2 bg-black bg-opacity-70 rounded">
                                <i class="fas fa-volume-${volume === 0 ? 'mute' : volume < 0.5 ? 'down' : 'up'} text-white mb-2"></i>
                                <div class="w-2 h-20 bg-gray-600 rounded">
                                    <div class="bg-white rounded" style="height: ${percentage}%; margin-top: ${100-percentage}%"></div>
                                </div>
                                <span class="mt-1 text-xs text-white">${percentage}%</span>
                            </div>
                        `;

                    clearTimeout(indicator.timeout);
                    indicator.timeout = setTimeout(() => indicator.remove(), 2000);
                }

                // ===== Auto-hide Controls =====
                let controlsTimeout;
                let isControlsVisible = true;

                function showControls() {
                    const controls = document.getElementById('videoControls');
                    if (controls) {
                        controls.style.opacity = '1';
                        isControlsVisible = true;
                        document.body.style.cursor = 'default';
                    }
                    resetControlsTimeout();
                }

                function hideControls() {
                    const controls = document.getElementById('videoControls');
                    if (controls && !watchVideo.paused) {
                        controls.style.opacity = '0';
                        isControlsVisible = false;
                        document.body.style.cursor = 'none';
                    }
                }

                function resetControlsTimeout() {
                    clearTimeout(controlsTimeout);
                    controlsTimeout = setTimeout(hideControls, 3000);
                }

                if (playerSection) {
                    playerSection.addEventListener('mousemove', showControls);
                    playerSection.addEventListener('click', (e) => {
                        if (e.target === playerSection || e.target === watchVideo) {
                            if (isControlsVisible) {
                                if (playBtn) playBtn.click();
                            } else {
                                showControls();
                            }
                        }
                    });
                }

                // ===== Picture in Picture =====
                function initPictureInPicture() {
                    if ('pictureInPictureEnabled' in document) {
                        const pipBtn = document.createElement('button');
                        pipBtn.className = 'p-2 rounded bg-black/50 hover:bg-black/70';
                        pipBtn.innerHTML = '<i class="fas fa-external-link-alt"></i>';
                        pipBtn.title = 'Picture in Picture';

                        pipBtn.addEventListener('click', async () => {
                            try {
                                if (document.pictureInPictureElement) {
                                    await document.exitPictureInPicture();
                                } else {
                                    await watchVideo.requestPictureInPicture();
                                }
                            } catch (error) {
                                console.error('PiP error:', error);
                            }
                        });

                        const controlsTop = document.querySelector('#videoControls > div:first-child');
                        if (controlsTop) controlsTop.appendChild(pipBtn);
                    }
                }

                // ===== Error Recovery =====
                function handleVideoError(error) {
                    console.error('Video error:', error);

                    if (watchVideo.error) {
                        const errorCode = watchVideo.error.code;
                        let userMessage = 'حدث خطأ في تشغيل الفيديو';

                        switch (errorCode) {
                            case 1:
                                userMessage = 'تم إلغاء تحميل الفيديو';
                                break;
                            case 2:
                                userMessage = 'خطأ في الشبكة. تحقق من اتصال الإنترنت';
                                break;
                            case 3:
                                userMessage = 'خطأ في فك تشفير الفيديو';
                                break;
                            case 4:
                                userMessage = 'تنسيق الفيديو غير مدعوم';
                                break;
                        }

                        showNotification(userMessage, 'error');
                    }
                }

                if (watchVideo) {
                    watchVideo.addEventListener('error', handleVideoError);
                }

                // ===== Network Status =====
                window.addEventListener('online', () => {
                    showNotification('تم استعادة الاتصال بالإنترنت', 'success');
                });

                window.addEventListener('offline', () => {
                    showNotification('انقطع الاتصال بالإنترنت', 'error');
                });

                // ===== Cleanup =====
                function cleanup() {
                    console.log('Cleaning up video player resources');

                    stopProgressTracking();

                    if (heroVideoTimer) {
                        clearTimeout(heroVideoTimer);
                        heroVideoTimer = null;
                    }

                    if (controlsTimeout) {
                        clearTimeout(controlsTimeout);
                        controlsTimeout = null;
                    }

                    if (heroVideo) {
                        heroVideo.pause();
                        heroVideo.currentTime = 0;
                    }

                    if (watchVideo) {
                        watchVideo.pause();
                    }

                    const loadingElement = document.getElementById('video-loading');
                    if (loadingElement) loadingElement.remove();

                    const volumeIndicator = document.getElementById('volume-indicator');
                    if (volumeIndicator) volumeIndicator.remove();
                }

                window.addEventListener('beforeunload', cleanup);

                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        if (heroVideo && !heroVideo.paused) heroVideo.pause();
                        if (watchVideo && !watchVideo.paused) stopProgressTracking();
                    } else {
                        if (watchVideo && !watchVideo.paused) startProgressTracking();
                    }
                });

                // ===== Initialize All Components =====
                console.log('Initializing all components...');

                try {
                    initHeroSection();
                    initWatchNowButtons();
                    initVideoControls();
                    initWatchlistButton();
                    initShareButton();
                    initTabs();
                    initCommentModal();
                    initSwiper();
                    initPictureInPicture();

                    console.log('All components initialized successfully ✅');

                } catch (error) {
                    console.error('Error during initialization:', error);
                    showNotification('حدث خطأ في تهيئة الصفحة', 'error');
                }
            });
        </script>
    @endpush
</x-front-layout>
