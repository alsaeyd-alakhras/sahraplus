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
    $q->whereIn('categories.id', $movie->categories->pluck('id'));
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
    $actors = $movie->cast()->actors()->with('person')->ordered()->get();
    $directors = $movie->cast()->directors()->with('person')->ordered()->get();
    $allCast = $movie->cast()->with('person')->ordered()->get();
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
    @push('styles')
    <!-- أضف CSS Plyr -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
    /* إصلاح اتجاه التحكم */
    .plyr__controls {
        direction: ltr !important;
    }

    .plyr {
        --plyr-color-main: #ef4444;
        --plyr-video-background: #000;
    }

    .plyr--fullscreen {
        z-index: 9999;
    }

    /* زر تخطي المقدمة */
    #skipIntroBtn {
        position: absolute;
        right: 20px;
        bottom: 100px;
        z-index: 1000;
        background: #ef4444;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: background 0.3s;
    }

    #skipIntroBtn:hover {
        background: #dc2626;
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
            <video id="plyrPlayer" playsinline controls data-poster="{{ $movie->backdrop_full_url }}"
                data-intro="{{ $movie->intro_skip_time ?? 0 }}">

                <!-- مصادر الفيديو المتعددة -->
                @if($movie->videoFiles && $movie->videoFiles->count() > 0)
                @foreach($movie->videoFiles->sortBy('quality') as $videoFile)
                <source src="{{ $videoFile->file_url }}" type="video/mp4"
                    size="{{ str_replace('p', '', $videoFile->quality) }}" />
                @endforeach
                @endif

                <!-- ملفات الترجمة -->
                @if ($movie->subtitles && $movie->subtitles->count() > 0)
                @foreach ($movie->subtitles as $index => $subtitle)
                <track kind="captions" label="{{ $subtitle->label }}" srclang="{{ $subtitle->language }}"
                    src="{{ $subtitle->file_url }}" @if ($index===0) default @endif />
                @endforeach
                @endif
            </video>

            <!-- زر تخطي المقدمة -->
            @if ($movie->intro_skip_time && $movie->intro_skip_time > 0)
            <button id="skipIntroBtn" class="hidden">
                ↩ تخطي المقدمة
            </button>
            @endif

            <!-- زر الخروج -->
            <button id="exitPlyrPlayer"
                class="absolute top-4 right-4 z-[10000] p-2 bg-black bg-opacity-50 rounded-full text-white hover:bg-opacity-75 transition-all">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                </svg>
            </button>
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
                                <img src="{{ $relatedMovie->poster_full_url }}" alt="{{ $relatedMovie->$title }}"
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
                            <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">
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
                            <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">
                                {{ $actor->person->name }}
                            </span>
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
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
    // بيانات الفيلم
    const movieData = {
        id: {
            {
                $movie - > id
            }
        },
        videoFiles: @json($videoFiles ?? []),
        subtitles: @json($movie - > subtitles ?? []),
        requireLogin: {
            {
                $requireLogin ? 'true' : 'false'
            }
        },
        requireSubscription: {
            {
                $requireSubscription ? 'true' : 'false'
            }
        },
        introSkipTime: {
            {
                $movie - > intro_skip_time ?? 0
            }
        }
    };

    // متغيرات الحالة
    let plyrInstance = null;
    const isAuthenticated = {
        {
            auth() - > check() ? 'true' : 'false'
        }
    };
    const watchProgress = @json($watchProgress ?? null);
    let progressInterval = null;
    let isPlayerReady = false;

    // DOM Elements
    const continueBtn = document.getElementById('continueWatching');
    const watchFromStartBtn = document.getElementById('watchFromStart');
    const watchNowBtns = document.querySelectorAll('#watchNow, .watchNowBtn');
    const playerSection = document.getElementById('playerSection');
    const exitBtn = document.getElementById('exitPlyrPlayer');
    const skipBtn = document.getElementById('skipIntroBtn');

    document.addEventListener('DOMContentLoaded', function() {
        console.log('تهيئة مشغل Plyr...');

        checkWatchProgress();
        initializeWatchButtons();

        console.log('تم تهيئة الأزرار بنجاح');
    });

    // فحص تقدم المشاهدة وعرض الأزرار المناسبة
    function checkWatchProgress() {
        if (isAuthenticated && watchProgress && watchProgress.progress_percentage > 5) {
            const progressSpan = document.getElementById('progressPercentage');
            if (progressSpan) {
                progressSpan.textContent = watchProgress.progress_percentage.toFixed(1);
            }
            if (continueBtn) continueBtn.classList.remove('hidden');
            if (watchFromStartBtn) watchFromStartBtn.classList.remove('hidden');

            // إخفاء زر شاهد الآن العادي
            watchNowBtns.forEach(btn => btn.classList.add('hidden'));
        }
    }

    // تهيئة مشغل Plyr
    function initializePlyrPlayer() {
        console.log('إنشاء مشغل Plyr...');

        const controls = [
            'play-large',
            'restart',
            'rewind',
            'play',
            'fast-forward',
            'progress',
            'current-time',
            'duration',
            'mute',
            'volume',
            'captions',
            'settings',
            'pip',
            'airplay',
            'fullscreen'
        ];

        // الحصول على قائمة الجودات المتاحة
        const qualityOptions = movieData.videoFiles ?
            movieData.videoFiles.map(file => parseInt(file.quality.replace('p', ''))) : [720];

        plyrInstance = new Plyr('#plyrPlayer', {
            controls,
            settings: ['captions', 'quality', 'speed'],
            quality: {
                default: getOptimalQuality(),
                // هذا السطر خاطئ - يجب أن يكون array من الأرقام
                options: movieData.videoFiles ? movieData.videoFiles.map(file => parseInt(file.quality.replace(
                    'p', ''))) : [720],
                forced: true,
                onChange: (newQuality) => {
                    console.log('تم تغيير الجودة إلى:', newQuality + 'p');
                    // لا تحتاج changeVideoQuality هنا - Plyr يتعامل معها تلقائياً
                }
            },
            captions: {
                active: false,
                update: true,
                language: 'ar'
            },
            keyboard: {
                focused: true,
                global: true
            },
            tooltips: {
                controls: true,
                seek: true
            },
            speed: {
                selected: 1,
                options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]
            }
        });

        // ربط الأحداث
        bindPlyrEvents();

        return plyrInstance;
    }

    // ربط أحداث المشغل
    function bindPlyrEvents() {
        if (!plyrInstance) return;

        // المشغل جاهز
        plyrInstance.on('ready', () => {
            isPlayerReady = true;
            console.log('مشغل Plyr جاهز');
        });

        // تحديث التقدم
        plyrInstance.on('timeupdate', () => {
            if (isAuthenticated && isPlayerReady) {
                updateWatchProgress();
                checkSkipIntro();
            }
        });

        // انتهاء الفيديو
        plyrInstance.on('ended', () => {
            console.log('انتهى الفيديو');
            stopProgressTracking();
            markAsCompleted();
        });

        // بدء التشغيل
        plyrInstance.on('play', () => {
            console.log('بدأ التشغيل');
            startProgressTracking();
        });

        // إيقاف التشغيل
        plyrInstance.on('pause', () => {
            console.log('توقف التشغيل');
            stopProgressTracking();
        });

        // أخطاء التشغيل
        plyrInstance.on('error', (event) => {
            console.error('خطأ في المشغل:', event);
        });
    }

    // فحص إظهار زر تخطي المقدمة
    function checkSkipIntro() {
        if (!movieData.introSkipTime || movieData.introSkipTime <= 0 || !skipBtn) return;

        const currentTime = plyrInstance.currentTime;

        // إظهار الزر إذا كنا في وقت المقدمة
        if (currentTime >= 5 && currentTime < movieData.introSkipTime) {
            skipBtn.classList.remove('hidden');
        } else {
            skipBtn.classList.add('hidden');
        }
    }

    // تهيئة أزرار المشاهدة
    function initializeWatchButtons() {
        // زر متابعة المشاهدة
        if (continueBtn) {
            continueBtn.addEventListener('click', () => {
                const startTime = watchProgress ? watchProgress.watched_seconds : 0;
                startVideoPlayer(startTime);
            });
        }

        // زر من البداية
        if (watchFromStartBtn) {
            watchFromStartBtn.addEventListener('click', () => {
                startVideoPlayer(0);
            });
        }

        // أزرار شاهد الآن
        watchNowBtns.forEach(btn => {
            btn.addEventListener('click', handleWatchNowClick);
        });

        // زر الخروج
        if (exitBtn) {
            exitBtn.addEventListener('click', exitPlyrPlayer);
        }

        // زر تخطي المقدمة
        if (skipBtn) {
            skipBtn.addEventListener('click', () => {
                if (plyrInstance && movieData.introSkipTime) {
                    plyrInstance.currentTime = movieData.introSkipTime;
                    skipBtn.classList.add('hidden');
                }
            });
        }
    }

    // التعامل مع النقر على زر شاهد الآن
    function handleWatchNowClick(e) {
        e.preventDefault();

        if (movieData.requireLogin && !isAuthenticated) {
            window.location.href = '/login';
            return;
        }

        if (movieData.requireSubscription && !hasActiveSubscription()) {
            window.location.href = '/subscribe';
            return;
        }

        startVideoPlayer(0);
    }

    // بدء تشغيل الفيديو
    async function startVideoPlayer(startTime = 0) {
        console.log('بدء تشغيل الفيديو من الوقت:', startTime);

        try {
            // إنشاء المشغل إذا لم يكن موجوداً
            if (!plyrInstance) {
                await initializePlyrPlayer();
            }

            // انتظار جاهزية المشغل
            if (!isPlayerReady) {
                await new Promise(resolve => {
                    const checkReady = () => {
                        if (isPlayerReady) {
                            resolve();
                        } else {
                            setTimeout(checkReady, 100);
                        }
                    };
                    checkReady();
                });
            }

            // عرض المشغل
            playerSection.classList.remove('hidden');

            // دخول الشاشة الكاملة
            try {
                await requestFullscreen();
            } catch (error) {
                console.log('لا يمكن دخول الشاشة الكاملة:', error.message);
            }

            // تشغيل الفيديو
            await plyrInstance.play();

            // تعيين وقت البداية
            if (startTime > 0) {
                plyrInstance.currentTime = startTime;
            }

            startProgressTracking();
            console.log('بدأ تشغيل الفيديو بنجاح');

        } catch (error) {
            console.error('فشل في تشغيل الفيديو:', error);
            playerSection.classList.add('hidden');
        }
    }

    // تحديد أفضل جودة فيديو
    function getOptimalQuality() {
        if (!movieData.videoFiles || movieData.videoFiles.length === 0) return 720;

        const screenWidth = window.screen.width;
        const availableQualities = movieData.videoFiles.map(f => parseInt(f.quality.replace('p', '')));

        let targetQuality = 720;
        if (screenWidth >= 1920) targetQuality = 1080;
        else if (screenWidth >= 1280) targetQuality = 720;
        else targetQuality = 480;

        const closest = availableQualities.reduce((prev, curr) =>
            Math.abs(curr - targetQuality) < Math.abs(prev - targetQuality) ? curr : prev
        );

        return closest;
    }

    // طلب الشاشة الكاملة
    function requestFullscreen() {
        return new Promise((resolve, reject) => {
            const element = playerSection;

            if (!document.fullscreenEnabled && !document.webkitFullscreenEnabled) {
                resolve();
                return;
            }

            let fullscreenPromise;

            if (element.requestFullscreen) {
                fullscreenPromise = element.requestFullscreen();
            } else if (element.webkitRequestFullscreen) {
                fullscreenPromise = element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) {
                fullscreenPromise = element.msRequestFullscreen();
            } else {
                resolve();
                return;
            }

            if (fullscreenPromise) {
                fullscreenPromise.then(resolve).catch(() => resolve());
            } else {
                resolve();
            }
        });
    }

    // الخروج من المشغل
    function exitPlyrPlayer() {
        stopProgressTracking();

        if (plyrInstance && isPlayerReady) {
            if (isAuthenticated && plyrInstance.currentTime > 0) {
                saveProgressToServer(plyrInstance.currentTime, plyrInstance.duration);
            }
            plyrInstance.pause();
        }

        // إخفاء زر تخطي المقدمة
        if (skipBtn) skipBtn.classList.add('hidden');

        // الخروج من الشاشة الكاملة
        if (document.exitFullscreen) {
            document.exitFullscreen().catch(e => console.log('Exit fullscreen error:', e));
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }

        setTimeout(() => {
            playerSection.classList.add('hidden');
        }, 300);
    }

    // تتبع التقدم
    function startProgressTracking() {
        if (progressInterval) clearInterval(progressInterval);

        progressInterval = setInterval(() => {
            if (plyrInstance && isAuthenticated && isPlayerReady) {
                saveProgressToServer(plyrInstance.currentTime, plyrInstance.duration);
            }
        }, 15000);
    }

    function stopProgressTracking() {
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }
    }

    function updateWatchProgress() {
        if (!plyrInstance || !isAuthenticated || !isPlayerReady) return;

        const currentTime = plyrInstance.currentTime;
        const duration = plyrInstance.duration;

        if (currentTime > 0 && duration > 0) {
            const progressPercent = (currentTime / duration) * 100;
            const progress = {
                movieId: movieData.id,
                currentTime: currentTime,
                duration: duration,
                progressPercent: progressPercent,
                timestamp: Date.now()
            };
            localStorage.setItem(`movie_progress_${movieData.id}`, JSON.stringify(progress));
        }
    }

    async function saveProgressToServer(currentTime, duration) {
        if (!isAuthenticated || !currentTime || !duration) return;

        try {
            const response = await fetch(`/api/movies/${movieData.id}/progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || ''
                },
                body: JSON.stringify({
                    current_time: currentTime,
                    duration: duration,
                    progress_percentage: (currentTime / duration) * 100
                })
            });

            if (response.ok) {
                console.log('تم حفظ التقدم');
            }
        } catch (error) {
            console.error('فشل في حفظ التقدم:', error);
        }
    }

    async function markAsCompleted() {
        if (!isAuthenticated) return;

        try {
            await fetch(`/api/movies/${movieData.id}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || ''
                }
            });
        } catch (error) {
            console.error('فشل في تحديد الفيلم كمكتمل:', error);
        }
    }

    function hasActiveSubscription() {
        return {
            {
                $hasSubscription ? 'true' : 'false'
            }
        };
    }

    // التعامل مع الخروج من الشاشة الكاملة
    // document.addEventListener('fullscreenchange', () => {
    //     if (!document.fullscreenElement) {
    //         exitPlyrPlayer();
    //     }
    // });

    // تنظيف الموارد
    window.addEventListener('beforeunload', () => {
        stopProgressTracking();
    });

    console.log('تم تحميل سكريبت مشغل Plyr بالكامل');
    </script>
    @endpush
</x-front-layout>
