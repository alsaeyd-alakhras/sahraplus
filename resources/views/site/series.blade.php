<x-front-layout>
    <!-- Hero Section -->
    <section id="hero" class="overflow-hidden relative h-screen">
        <!-- Slides -->
        @foreach ($seriesHero as $series)
            <div class="absolute inset-0 opacity-100 hero-slide">
                <img src="{{ $series->backdrop_full_url }}" alt="Hero Background"
                    class="object-cover absolute inset-0 w-full h-full" />
                <video src="{{ $series->trailer_full_url }}" class="hidden object-cover absolute inset-0 w-full h-full"
                    playsinline></video>
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
                        <img src="" alt="logo"
                            class="object-contain h-full transition-all duration-300 hover:scale-125" />
                    </div>
                    <div class="text-base text-gray-400 transition-all duration-300 episode animate-slide-up">
                        الموسم 1، الحلقة 1
                    </div>
                    <div class="flex items-center my-4 space-x-2 rtl:space-x-reverse animate-slide-up">
                        <button
                            class="flex items-center px-2 py-2 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <a href="#"
                            class="flex items-center px-8 py-2 space-x-2 text-lg font-bold text-white rounded-lg transition-all duration-300 bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <span>شاهد الآن</span>
                        </a>
                    </div>
                    <p
                        class="mb-6 max-w-xl text-xl leading-relaxed text-gray-200 transition-all duration-300 md:text-lg animate-slide-up description">
                        بعد خيانة أصدقائه والمرأة التي أحبها...
                    </p>
                    <div
                        class="flex flex-wrap items-center mb-6 space-x-3 text-sm text-gray-400 rtl:space-x-reverse tags animate-slide-up">
                        <!-- يتم توليدها ديناميكيًا -->
                    </div>

                </div>
            </div>
        </div>

        <!-- Hero Navigation Dots -->
        <div class="flex absolute bottom-8 left-1/2 z-20 space-x-3 transform -translate-x-1/2 rtl:space-x-reverse">
            @foreach ($seriesHero as $series)
                <button
                    class="w-full h-[72px] hero-dot opacity-75 hover:opacity-100 hover:scale-110 transition-all duration-300"><img
                        src="{{ $series->poster_full_url }}" alt="logo1"
                        class="object-contain w-full h-full" /></button>
            @endforeach
        </div>
    </section>

    <!-- Movie Sections Container -->
    <div id="movie-sections-container">
        <!-- Loading Indicator -->
        <div id="loading" class="flex justify-center items-center py-16">
            <div class="w-12 h-12 rounded-full border-b-2 border-red-500 animate-spin"></div>
            <span class="mr-3 text-lg">جاري التحميل...</span>
        </div>

    </div>

    <!-- Load More Indicator -->
    <div id="load-more-indicator" class="flex hidden justify-center items-center py-8">
        <div class="w-8 h-8 rounded-full border-b-2 border-red-500 animate-spin"></div>
        <span class="mr-3">تحميل المزيد...</span>
    </div>

    <!-- Scripts -->
    @push('scripts')
        <script>
            $(document).ready(function() {
                let loadedSections = [];
                let isLoading = false;
                let currentHeroIndex = 0;
                let swiperCounter = 0;

                loadSections();
                /**
                 * تحميل الأقسام من API
                 */
                function loadSections() {
                    if (isLoading) return;

                    isLoading = true;
                    $('#loading').show();

                    $.ajax({
                        url: "{{ route('site.series.sections') }}",
                        method: 'GET',
                        data: {
                            loaded: loadedSections
                        },
                        success: function(response) {
                            if (response.success && response.sections.length > 0) {
                                response.sections.forEach(function(section) {
                                    renderSection(section);
                                    loadedSections.push(section.name);
                                });
                            }

                            $('#loading').hide();
                            isLoading = false;
                        },
                        error: function(xhr, status, error) {
                            console.error('خطأ في تحميل الأقسام:', error);
                            $('#loading').hide();
                            showError('حدث خطأ أثناء تحميل الأفلام');
                            isLoading = false;
                        }
                    });
                }

                /**
                 * تحميل أقسام إضافية (Lazy Loading)
                 */
                function loadMoreSections() {
                    if (isLoading) return;

                    isLoading = true;
                    $('#load-more-indicator').show();

                    $.ajax({
                        url: "{{ route('site.series.sections') }}",
                        method: 'GET',
                        data: {
                            loaded: loadedSections
                        },
                        success: function(response) {
                            if (response.success && response.sections.length > 0) {
                                response.sections.forEach(function(section) {
                                    renderSection(section);
                                    loadedSections.push(section.name);
                                });
                            }

                            $('#load-more-indicator').hide();
                            isLoading = false;
                        },
                        error: function(xhr, status, error) {
                            console.error('خطأ في تحميل المزيد:', error);
                            $('#load-more-indicator').hide();
                            isLoading = false;
                        }
                    });
                }

                /**
                 * رسم قسم الأفلام
                 */
                function renderSection(section) {
                    if (section.movies.length === 0) return;

                    $.ajax({
                        url: "{{ route('site.series.get-html-section') }}",
                        method: 'GET',
                        data: {
                            title_section: section.title,
                            items: section.movies,
                            display_type: section.display_type
                        },
                        success: function(response) {
                            $('#movie-sections-container').append(response);
                            initializeSwiper();
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.error('خطأ في تحميل المزيد:', error);
                            toast.error('حدث خطأ أثناء تحميل الأفلام');
                            $('#load-more-indicator').hide();
                            isLoading = false;
                        }
                    });
                }

                // /**
                //  * تهيئة Swiper
                //  */
                function initializeSwiper() {
                    try {
                        new Swiper(".mySwiper-vertical", {
                            slidesPerView: 6.2,
                            spaceBetween: 10,
                            navigation: {
                                nextEl: ".swiper-button-next",
                                prevEl: ".swiper-button-prev",
                            },
                            rtl: true,
                            breakpoints: {
                                320: { slidesPerView: 3.2 },
                                640: { slidesPerView: 4.2 },
                                1024: { slidesPerView: 6.2 },
                            },
                            on: {
                                init: toggleNavButtons,
                                slideChange: toggleNavButtons,
                                resize: toggleNavButtons,
                            },
                        });
                        new Swiper(".mySwiper-horizontal", {
                            slidesPerView: 5.2,
                            spaceBetween: 20,
                            navigation: {
                                nextEl: ".swiper-button-next",
                                prevEl: ".swiper-button-prev",
                            },
                            rtl: true,
                            breakpoints: {
                                320: { slidesPerView: 2.2 },
                                640: { slidesPerView: 3.2 },
                                1024: { slidesPerView: 5.2 },
                            },
                            on: {
                                init: toggleNavButtons,
                                slideChange: toggleNavButtons,
                                resize: toggleNavButtons,
                            },
                        });
                    } catch (error) {
                        console.error('خطأ في تهيئة Swiper:', error);
                    }
                }

                /**
                 * عرض رسالة خطأ
                 */
                function showError(message) {
                    let errorHtml = `
                    <div class="bg-red-600 text-white p-4 rounded-lg mb-6 text-center max-w-[95%] mx-auto">
                        <p>${message}</p>
                        <button onclick="location.reload()" class="px-4 py-2 mt-2 bg-red-700 rounded transition-colors duration-200 hover:bg-red-800">
                            إعادة المحاولة
                        </button>
                    </div>
                `;
                    $('#movie-sections-container').append(errorHtml);
                }
            });
        </script>
        <script>
            const contents = @json($seriesHeroArray);
        </script>
    @endpush

</x-front-layout>
