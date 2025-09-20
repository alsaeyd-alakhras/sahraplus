<x-front-layout>
    @php
        $title = 'title_' . app()->getLocale();
        $description = 'description_' . app()->getLocale();
    @endphp
    @push('styles')
        <!-- Swiper -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
        <!-- Custom CSS -->
        <style>
            /* تحسين ظهور الأزرار */
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
        </style>
    @endpush
    <!-- Hero Section from index -->
    <section id="hero" class="overflow-hidden relative h-screen">
        <div class="absolute inset-0 opacity-100 hero-slide">
            <img src="{{$movie->backdrop_full_url}}" alt="Hero Background"
                class="object-cover absolute inset-0 w-full h-full" />
            <video id="heroVideo" src="{{$movie->trailer_full_url}}"
                class="hidden object-cover absolute inset-0 w-full h-full" playsinline></video>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        <!-- Mute/Unmute Button -->
        <button id="muteBtn" class="absolute left-10 top-28 z-20 p-3 text-white rounded-full bg-black/60"
            data-state="muted">
            <svg class="w-6 h-6 mute-icon" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z">
                </path>
            </svg>
        </button>
        <!-- Content Container -->
        <div class="flex relative z-10 items-end pb-20 h-full">
            <div class="container px-6 mx-auto">
                <div class="max-w-2xl text-white">
                    <!-- Logo -->
                    <div class="h-[60px] mb-4">
                        <img src="{{$movie->poster_full_url}}" alt="logo" class="object-contain h-full" />
                    </div>

                    <!-- Episode Info -->
                    <div class="mb-2 text-sm text-gray-300">{{$movie->$title}}</div>

                    <!-- Description -->
                    <p class="mb-4 text-base leading-relaxed text-gray-200 md:text-lg">
                        {{$movie->$description}}
                    </p>

                    <!-- Progress & Rating -->
                    <div class="flex justify-between items-center mb-4">
                        {{-- <!-- الوقت المتبقي وشريط التقدم -->
                        <div class="flex gap-2 items-center text-sm text-green-400">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <div class="flex flex-col">
                                <span>متبقي 27 دقيقة</span>
                                <div class="overflow-hidden mt-1 w-[30rem] h-2 bg-gray-300 rounded-full">
                                    <div class="h-full bg-green-500" style="width: 45%;"></div> <!-- نسبة المشاهدة -->
                                </div>
                            </div>
                        </div> --}}

                        <!-- التقييم -->
                        <div class="text-sm font-bold text-yellow-400">
                            ⭐ {{$movie->imdb_rating}}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-4 items-center">
                        <a href="#" id="watchNow"
                            class="flex items-center px-6 py-2 text-sm font-bold text-white rounded-lg transition-all bg-fire-red hover:bg-red-700">
                            <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            {{__('site.watch_now')}}
                        </a>

                        <button
                            class="flex gap-2 items-center px-5 py-2 text-sm font-bold text-white bg-gray-700 rounded-lg hover:bg-gray-600">
                            <i class="fas fa-plus"></i>
                            {{__('site.add_to_watchlist')}}
                        </button>

                        <!-- زر المشاركة -->
                        <div class="inline-block relative" id="share-container">
                            <button id="copy-link"
                                class="flex gap-2 items-center px-5 py-2 text-sm text-white transition-all duration-300 hover:text-sky-400">
                                <i class="fas fa-share-alt"></i>
                                {{__('site.share')}}
                            </button>

                            <!-- تنبيه النسخ -->
                            <div id="copy-alert"
                                class="absolute right-0 px-3 py-1 mt-2 text-xs text-green-500 bg-white bg-opacity-10 rounded-md shadow opacity-0 transition-opacity duration-300 pointer-events-none">
                                {{__('site.copied')}} ✅
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Content -->
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
                        <button id="watchNow"
                            class="flex items-center px-8 py-2 space-x-2 text-lg font-bold text-white rounded-lg transition-all duration-300 bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <span>شاهد الآن</span>
                        </button>
                    </div>
                    <p
                        class="mb-6 max-w-xl text-xl leading-relaxed text-gray-200 transition-all duration-300 md:text-lg animate-slide-up description">
                        بعد خيانة أصدقائه والمرأة التي أحبها، يجد مجد نفسه خلف القضبان...
                    </p>
                    <div
                        class="flex flex-wrap items-center mb-6 space-x-3 text-sm text-gray-400 rtl:space-x-reverse tags animate-slide-up">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ✅ Custom Fullscreen Video Player -->
    <section id="playerSection" class="hidden fixed inset-0 z-[9999] bg-black">
        <div class="relative w-full h-full">
            <!-- فيديو -->
            <!-- data-intro="60" وقت المقدمة بالثواني  -->
            <video id="watchVideo" class="object-cover z-10 w-full rounded-lg" src="./assets/videos/mov_bbb2.mp4"
                playsinline data-intro="60"></video>

            <button id="skipIntroBtn"
                class="hidden absolute right-5 bottom-16 z-30 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded transition bo-5 b-5 hover:bg-red-700">
                ⏩ تخطي المقدمة
            </button>
            <!-- ✅ أزرار التحكم -->
            <div id="videoControls"
                class="flex absolute inset-0 z-20 flex-col justify-between p-4 text-white pointer-events-none">
                <!-- الأعلى -->
                <div class="flex justify-between items-center pointer-events-auto">
                    <button id="toggleMute" class="p-2 rounded bg-black/50 hover:bg-black/70">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    <button id="nextVideo" data-next="movie2.html" class="p-2 rounded bg-black/50 hover:bg-black/70">
                        <i class="fas fa-forward"></i>
                    </button>
                </div>



                <!-- الأسفل الجديد -->
                <div class="flex gap-4 items-center w-full pointer-events-auto" dir="ltr">
                    <span id="currentTime" class="w-12 text-xs text-right text-gray-300">0:00</span>

                    <input type="range" id="progressBar" min="0" value="0"
                        class="w-full h-1 bg-gray-600 rounded cursor-pointer accent-sky-400" />

                    <span id="duration" class="w-12 text-xs text-left text-gray-300">0:00</span>

                    <!-- الأزرار -->
                    <div class="flex gap-3 items-center pl-4" dir="rtl">
                        <button id="rewind" class="p-2 rounded bg-black/50 hover:bg-black/70">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button id="togglePlay" class="p-3 rounded-full bg-black/50 hover:bg-black/70">
                            <i id="playIcon" class="fas fa-pause"></i>
                        </button>
                        <button id="forward" class="p-2 rounded bg-black/50 hover:bg-black/70">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Slider يظهر بعد انتهاء الفيديو -->
            <div id="nextEpisodesSlider"
                class="transition-all duration-700 absolute bottom-0 left-0 w-full z-[9999] bg-black bg-opacity-70 pointer-events-auto hidden"
                style="transform: translateY(100%); opacity: 0;">
                <!-- horizontal slider -->
                <div class="overflow-visible mb-6 px-4 py-6 mx-auto max-w-[95%]">
                    <!-- عنوان القسم -->
                    <h2 class="mb-4 text-2xl font-bold text-right">أفلام ذات صلة</h2>

                    <!-- سلايدر Swiper -->
                    <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
                        <div class="swiper-wrapper">
                            <script>
                                const movies2 = [
                                    "Hello+World",
                                    "Movie+1",
                                    "Guardians",
                                    "Lost+City",
                                    "Action+Show",
                                    "Romance+Story",
                                    "The+Heist",
                                    "Last+Stand",
                                    "Arab+Drama",
                                    "Old+Legends",
                                ];
                                for (let title of movies2) {
                                    document.write(`
                                <div class="swiper-slide">
                                    <div class="movie-slider-card">
                                    <img src="https://placehold.co/320x190?text=${title}" alt="${title}" class="object-cover w-full rounded-md aspect-video">
                                    <div class="movie-slider-details">
                                        <h3 class="text-lg font-bold">${title.replace(
                                            "+",
                                            " "
                                        )}</h3>
                                        <div class="movie-slider-line">
                                        <span>01:46:34</span>
                                        <span class="text-green-400">•</span>
                                        <span>كوميدي</span>
                                        <span class="text-green-400">•</span>
                                        <span>رومانسي</span>
                                        </div>
                                        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                        البطل الذي لا يريد القوة
                                        </div>
                                        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                            <button
                                                class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                            <a href="#"
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
                                `);
                                }
                            </script>
                        </div>

                        <!-- الأسهم -->
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
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red">ذات
                صلة</button>
            <button data-tab="details"
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red">التفاصيل</button>
            <button data-tab="cast"
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red">الممثلين</button>
            <button data-tab="comments"
                class="flex-1 px-4 py-2 text-center bg-gray-800 rounded-md transition-all duration-300 tab hover:bg-fire-red">التعليقات</button>
        </div>

        <!-- Tab Content -->
        <div>
            <div id="episodes" class="tab-content animate-fade-in">
                <div class="overflow-visible px-4 py-6 mx-auto mb-3">
                    <!-- عنوان القسم -->
                    <h2 class="mb-4 text-2xl font-bold text-right">أفلام ذات صلة</h2>

                    <!-- سلايدر Swiper -->
                    <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
                        <div class="swiper-wrapper">
                            <script>
                                const movies4 = [
                                    "Hello+World",
                                    "Movie+1",
                                    "Guardians",
                                    "Lost+City",
                                    "Action+Show",
                                    "Romance+Story",
                                    "The+Heist",
                                    "Last+Stand",
                                    "Arab+Drama",
                                    "Old+Legends",
                                ];
                                for (let title of movies4) {
                                    document.write(`
                                  <div class="swiper-slide">
                                    <div class="movie-slider-card">
                                      <img src="https://placehold.co/320x190?text=${title}" alt="${title}" class="object-cover w-full rounded-md aspect-video">
                                      <div class="movie-slider-details">
                                        <h3 class="text-lg font-bold">${title.replace(
                                            "+",
                                            " "
                                        )}</h3>
                                        <div class="movie-slider-line">
                                          <span>01:46:34</span>
                                          <span class="text-green-400">•</span>
                                          <span>كوميدي</span>
                                          <span class="text-green-400">•</span>
                                          <span>رومانسي</span>
                                        </div>
                                        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                          البطل الذي لا يريد القوة
                                        </div>
                                        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                            <button
                                                class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                            <a href="#"
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
                                `);
                                }
                            </script>
                        </div>

                        <!-- الأسهم -->
                        <div class="text-white swiper-button-next"></div>
                        <div class="text-white swiper-button-prev"></div>
                    </div>
                </div>

                <div class="overflow-visible px-4 py-6 mx-auto mb-3">
                    <!-- عنوان القسم -->
                    <h2 class="mb-4 text-2xl font-bold text-right">أعلى المشاهدة</h2>

                    <!-- سلايدر Swiper -->
                    <div class="isolate overflow-visible relative pb-44 swiper mySwiper-horizontal">
                        <div class="swiper-wrapper">
                            <script>
                                const movies3 = [
                                    "Hello+World",
                                    "Movie+1",
                                    "Guardians",
                                    "Lost+City",
                                    "Action+Show",
                                    "Romance+Story",
                                    "The+Heist",
                                    "Last+Stand",
                                    "Arab+Drama",
                                    "Old+Legends",
                                ];
                                for (let title of movies3) {
                                    document.write(`
                                  <div class="swiper-slide">
                                    <div class="movie-slider-card">
                                      <img src="https://placehold.co/320x190?text=${title}" alt="${title}" class="object-cover w-full rounded-md aspect-video">
                                      <div class="movie-slider-details">
                                        <h3 class="text-lg font-bold">${title.replace(
                                            "+",
                                            " "
                                        )}</h3>
                                        <div class="movie-slider-line">
                                          <span>01:46:34</span>
                                          <span class="text-green-400">•</span>
                                          <span>كوميدي</span>
                                          <span class="text-green-400">•</span>
                                          <span>رومانسي</span>
                                        </div>
                                        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                          البطل الذي لا يريد القوة
                                        </div>
                                        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                            <button
                                                class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                            <a href="#"
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
                                `);
                                }
                            </script>
                        </div>

                        <!-- الأسهم -->
                        <div class="text-white swiper-button-next"></div>
                        <div class="text-white swiper-button-prev"></div>
                    </div>
                </div>
            </div>
            <div id="details" class="hidden tab-content animate-fade-in">
                <h2 class="pb-2 mb-6 text-2xl font-bold text-white border-b border-gray-600">تفاصيل الفيلم</h2>
                <div class="grid grid-cols-1 gap-6 text-gray-300 md:grid-cols-2">

                    <!-- وصف -->
                    <div class="col-span-2">
                        <p class="text-lg leading-relaxed">
                            قصة <span class="font-semibold text-sky-400">تشويقية</span> تدور حول بطل يسعى للانتقام بعد
                            خيانة أصدقائه، حيث تتصاعد الأحداث
                            في قالب من <span class="font-semibold text-red-400">الأكشن والإثارة</span>.
                        </p>
                    </div>

                    <!-- التصنيف -->
                    <div class="flex gap-3 items-center">
                        <i class="text-sky-400 fas fa-film"></i>
                        <span><span class="font-semibold text-white">التصنيف:</span> أكشن، دراما</span>
                    </div>

                    <!-- المدة -->
                    <div class="flex gap-3 items-center">
                        <i class="text-yellow-400 fas fa-clock"></i>
                        <span><span class="font-semibold text-white">المدة:</span> 45 دقيقة</span>
                    </div>

                    <!-- سنة الإنتاج -->
                    <div class="flex gap-3 items-center">
                        <i class="text-green-400 fas fa-calendar-alt"></i>
                        <span><span class="font-semibold text-white">سنة الإنتاج:</span> 2024</span>
                    </div>

                </div>
            </div>
            <div id="cast" class="hidden tab-content animate-fade-in">
                <h2 class="pb-2 mb-6 text-2xl font-bold text-white border-b border-gray-600">الممثلين</h2>

                <div class="grid grid-cols-2 gap-6 text-center sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    <!-- ممثل 1 -->
                    <a href="#actor1" class="transition-transform duration-300 group hover:scale-105">
                        <div class="overflow-hidden rounded-lg shadow-md">
                            <img src="https://placehold.co/150x200" alt="ممثل 1"
                                class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                        </div>
                        <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">ممثل
                            1</span>
                    </a>

                    <!-- ممثل 2 -->
                    <a href="#actor2" class="transition-transform duration-300 group hover:scale-105">
                        <div class="overflow-hidden rounded-lg shadow-md">
                            <img src="https://placehold.co/150x200" alt="ممثل 2"
                                class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                        </div>
                        <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">ممثل
                            2</span>
                    </a>

                    <!-- ممثل 3 -->
                    <a href="#actor3" class="transition-transform duration-300 group hover:scale-105">
                        <div class="overflow-hidden rounded-lg shadow-md">
                            <img src="https://placehold.co/150x200" alt="ممثل 3"
                                class="object-cover w-full h-52 rounded-lg group-hover:opacity-90" />
                        </div>
                        <span class="block mt-2 text-sm font-semibold text-gray-300 group-hover:text-white">ممثل
                            3</span>
                    </a>

                    <!-- ممثل 4 -->
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
            <div id="comments" class="hidden tab-content animate-fade-in">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">التعليقات</h2>
                    <button id="openCommentModal"
                        class="px-4 py-1 text-sm text-white rounded transition-all bg-fire-red hover:bg-red-700">
                        + أضف تعليق
                    </button>
                </div>

                <!-- التعليقات لنفس المستخدم -->
                <div id="commentsList" class="space-y-4">
                    <!-- تعليق 1 -->
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
                            <!-- تكرار التعليق -->

                            <div class="swiper-slide bg-gray-900 text-white p-4 rounded-xl w-[280px] shadow-md">
                                <div
                                    class="flex gap-4 items-start p-4 text-white bg-gray-800 bg-opacity-50 rounded-xl shadow-lg">
                                    <!-- صورة المستخدم -->
                                    <div class="relative">
                                        <img src="./assets/images/avatar.jpg" alt="Avatar"
                                            class="w-12 h-12 rounded-full ring-2 ring-gray-600 shadow-sm">
                                    </div>

                                    <!-- المحتوى -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-white">مستخدم 1</p>
                                            <div class="text-end">
                                                <div class="flex gap-1 text-sm text-yellow-400">
                                                    ⭐⭐⭐⭐⭐
                                                </div>
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
                            <div class="swiper-slide bg-gray-900 text-white p-4 rounded-xl w-[280px] shadow-md">
                                <div
                                    class="flex gap-4 items-start p-4 text-white bg-gray-800 bg-opacity-50 rounded-xl shadow-lg">
                                    <!-- صورة المستخدم -->
                                    <div class="relative">
                                        <img src="./assets/images/avatar.jpg" alt="Avatar"
                                            class="w-12 h-12 rounded-full ring-2 ring-gray-600 shadow-sm">
                                    </div>

                                    <!-- المحتوى -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-white">مستخدم 1</p>
                                            <div class="text-end">
                                                <div class="flex gap-1 text-sm text-yellow-400">
                                                    ⭐⭐⭐⭐⭐
                                                </div>
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
                            <div class="swiper-slide bg-gray-900 text-white p-4 rounded-xl w-[280px] shadow-md">
                                <div
                                    class="flex gap-4 items-start p-4 text-white bg-gray-800 bg-opacity-50 rounded-xl shadow-lg">
                                    <!-- صورة المستخدم -->
                                    <div class="relative">
                                        <img src="./assets/images/avatar.jpg" alt="Avatar"
                                            class="w-12 h-12 rounded-full ring-2 ring-gray-600 shadow-sm">
                                    </div>

                                    <!-- المحتوى -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-white">مستخدم 1</p>
                                            <div class="text-end">
                                                <div class="flex gap-1 text-sm text-yellow-400">
                                                    ⭐⭐⭐⭐⭐
                                                </div>
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
                            <div class="swiper-slide bg-gray-900 text-white p-4 rounded-xl w-[280px] shadow-md">
                                <div
                                    class="flex gap-4 items-start p-4 text-white bg-gray-800 bg-opacity-50 rounded-xl shadow-lg">
                                    <!-- صورة المستخدم -->
                                    <div class="relative">
                                        <img src="./assets/images/avatar.jpg" alt="Avatar"
                                            class="w-12 h-12 rounded-full ring-2 ring-gray-600 shadow-sm">
                                    </div>

                                    <!-- المحتوى -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-white">مستخدم 1</p>
                                            <div class="text-end">
                                                <div class="flex gap-1 text-sm text-yellow-400">
                                                    ⭐⭐⭐⭐⭐
                                                </div>
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
                            <div class="swiper-slide bg-gray-900 text-white p-4 rounded-xl w-[280px] shadow-md">
                                <div
                                    class="flex gap-4 items-start p-4 text-white bg-gray-800 bg-opacity-50 rounded-xl shadow-lg">
                                    <!-- صورة المستخدم -->
                                    <div class="relative">
                                        <img src="./assets/images/avatar.jpg" alt="Avatar"
                                            class="w-12 h-12 rounded-full ring-2 ring-gray-600 shadow-sm">
                                    </div>

                                    <!-- المحتوى -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-white">مستخدم 1</p>
                                            <div class="text-end">
                                                <div class="flex gap-1 text-sm text-yellow-400">
                                                    ⭐⭐⭐⭐⭐
                                                </div>
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
                            <div class="swiper-slide bg-gray-900 text-white p-4 rounded-xl w-[280px] shadow-md">
                                <div
                                    class="flex gap-4 items-start p-4 text-white bg-gray-800 bg-opacity-50 rounded-xl shadow-lg">
                                    <!-- صورة المستخدم -->
                                    <div class="relative">
                                        <img src="./assets/images/avatar.jpg" alt="Avatar"
                                            class="w-12 h-12 rounded-full ring-2 ring-gray-600 shadow-sm">
                                    </div>

                                    <!-- المحتوى -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-white">مستخدم 1</p>
                                            <div class="text-end">
                                                <div class="flex gap-1 text-sm text-yellow-400">
                                                    ⭐⭐⭐⭐⭐
                                                </div>
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

                            <!-- كرر العناصر حسب الحاجة -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal -->
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
                document.querySelectorAll('.tab').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const target = this.getAttribute('data-tab');
                        document.querySelectorAll('.tab').forEach(b => b.classList.remove(
                            'bg-fire-red'));
                        document.querySelectorAll('.tab-content').forEach(c => c.classList.add(
                            'hidden'));
                        document.getElementById(target).classList.remove('hidden');
                        this.classList.add('bg-fire-red');
                    });
                });
            });
        </script>
        <script>
            document.getElementById('copy-link').addEventListener('click', function() {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    const alert = document.getElementById('copy-alert');
                    alert.classList.add('opacity-100');
                    alert.classList.remove('opacity-0');

                    setTimeout(() => {
                        alert.classList.remove('opacity-100');
                        alert.classList.add('opacity-0');
                    }, 2500); // إخفاء بعد 2.5 ثانية
                }).catch(err => {
                    console.error('فشل النسخ:', err);
                });
            });
        </script>
        <script>
            const wrapper = document.getElementById("playerSection");
            const video = document.getElementById("watchVideo");
            const playBtn = document.getElementById("togglePlay");
            const playIcon = document.getElementById("playIcon");
            const rewindBtn = document.getElementById("rewind");
            const forwardBtn = document.getElementById("forward");
            const muteBtn = document.getElementById("toggleMute");
            const nextBtn = document.getElementById("nextVideo");
            const nextSlider = document.getElementById("nextEpisodesSlider");

            // ✅ إظهار المشغل عند الضغط على زر "شاهد الآن"
            document.getElementById("watchNow").addEventListener("click", function() {
                wrapper.classList.remove("hidden");
                video.classList.remove("hidden");
                video.currentTime = 0;
                video.volume = 1;
                video.muted = false;
                video.play();

                if (wrapper.requestFullscreen) wrapper.requestFullscreen();
                else if (wrapper.webkitRequestFullscreen) wrapper.webkitRequestFullscreen();
                else if (wrapper.msRequestFullscreen) wrapper.msRequestFullscreen();
            });

            // ✅ عند الخروج من FullScreen: إخفاء الفيديو والأزرار
            document.addEventListener("fullscreenchange", () => {
                if (!document.fullscreenElement) {
                    video.pause();
                    wrapper.classList.add("hidden");
                    video.classList.add("hidden");
                }
            });


            // ✅ التشغيل / الإيقاف
            playBtn.addEventListener("click", () => {
                if (video.paused) {
                    video.play();
                    playIcon.classList.replace("fa-play", "fa-pause");
                } else {
                    video.pause();
                    playIcon.classList.replace("fa-pause", "fa-play");
                }
            });

            // ✅ التقديم / التأخير
            forwardBtn.addEventListener("click", () => video.currentTime += 10);
            rewindBtn.addEventListener("click", () => video.currentTime -= 10);

            // ✅ كتم / تشغيل الصوت
            muteBtn.addEventListener("click", () => {
                video.muted = !video.muted;
                muteBtn.innerHTML = video.muted ? '<i class="fas fa-volume-mute"></i>' :
                    '<i class="fas fa-volume-up"></i>';
            });

            // ✅ الانتقال للفيديو التالي
            nextBtn.addEventListener("click", () => {
                const next = nextBtn.dataset.next;
                if (next) window.location.href = next;
            });

            // ✅ عند انتهاء الفيديو: الخروج من Fullscreen فقط
            video.addEventListener("ended", () => {
                // // الخروج من Fullscreen
                // if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
                //     if (document.exitFullscreen) document.exitFullscreen();
                //     else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                //     else if (document.msExitFullscreen) document.msExitFullscreen();
                // }

                // إظهار السلايدر
                nextSlider.style.transform = "translateY(0%)";
                nextSlider.style.opacity = "1";
                nextSlider.classList.remove("hidden");
            });


            const progressBar = document.getElementById("progressBar");
            const currentTimeLabel = document.getElementById("currentTime");
            const durationLabel = document.getElementById("duration");

            // تحديث الوقت الظاهر
            video.addEventListener("timeupdate", () => {
                progressBar.value = video.currentTime;
                currentTimeLabel.textContent = formatTime(video.currentTime);
            });

            // عند تحميل الفيديو: حدد المدة
            video.addEventListener("loadedmetadata", () => {
                progressBar.max = video.duration;
                durationLabel.textContent = formatTime(video.duration);
            });

            // التحكم بالسحب
            progressBar.addEventListener("input", () => {
                video.currentTime = progressBar.value;
            });

            // تنسيق الوقت 0:00
            function formatTime(sec) {
                const minutes = Math.floor(sec / 60);
                const seconds = Math.floor(sec % 60).toString().padStart(2, "0");
                return `${minutes}:${seconds}`;
            }

            // ✅ إظهار زر التخطي
            const skipBtn = document.getElementById("skipIntroBtn");

            // ✅ إظهار الزر بعد بداية الفيديو بفترة قصيرة
            video.addEventListener("timeupdate", () => {
                const introTime = parseFloat(video.dataset.intro); // الوقت المحدد للتخطي
                if (!isNaN(introTime)) {
                    // إظهار الزر فقط إذا الوقت الحالي أقل من وقت التخطي بقليل
                    if (video.currentTime > 5 && video.currentTime < introTime) {
                        skipBtn.classList.remove("hidden");
                    } else {
                        skipBtn.classList.add("hidden");
                    }
                }
            });

            // ✅ تنفيذ التخطي عند الضغط
            skipBtn.addEventListener("click", () => {
                const introTime = parseFloat(video.dataset.intro);
                if (!isNaN(introTime)) {
                    video.currentTime = introTime;
                    skipBtn.classList.add("hidden");
                }
            });
        </script>
        <script>
            const openModal = document.getElementById("openCommentModal");
            const closeModal = document.getElementById("closeCommentModal");
            const modal = document.getElementById("commentModal");

            openModal.addEventListener("click", () => modal.classList.remove("hidden"));
            closeModal.addEventListener("click", () => modal.classList.add("hidden"));

            document.getElementById("submitComment").addEventListener("click", () => {
                const text = document.getElementById("commentInput").value.trim();
                if (text !== "") {
                    const modal = document.getElementById("commentModal");
                    const commentsList = document.getElementById("commentsList");

                    // إنشاء عنصر التعليق الجديد
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

                    // إدراج التعليق أول القائمة
                    commentsList.prepend(newComment);

                    // تنظيف وإغلاق المودال
                    document.getElementById("commentInput").value = "";
                    modal.classList.add("hidden");
                }
            });
        </script>
    @endpush

</x-front-layout>
