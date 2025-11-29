<x-front-layout>
    <!-- Hero Section -->
    <section id="hero" class="overflow-hidden relative h-screen">
        <div class="absolute inset-0 opacity-100 hero-slide">
            <img src="{{ asset('assets/images/slider/slider1.avif') }}" alt="Hero Background"
                class="object-cover absolute inset-0 w-full h-full" />
            <video id="heroVideo" src="http://filmszone.shop:8484/stream/test/ssc/master.m3u8?u=admin&p=8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918"
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
                        <img src="{{ asset('assets/images/logos/mbs1.png') }}" alt="logo" class="object-contain h-full" />
                    </div>


                    <!-- Description -->
                    <p class="mb-4 text-base leading-relaxed text-gray-200 md:text-lg">
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quo nesciunt similique veritatis optio, corrupti eos. Magnam totam rem distinctio illum!
                    </p>

                    <!-- Progress & Rating -->
                    <div>
                        <!-- التقييم -->
                        <div class="text-sm font-bold text-yellow-400">
                            ⭐ 8.9
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-4 items-center">
                        <a href="#" id="watchNow"
                            class="flex items-center px-6 py-2 text-sm font-bold text-white rounded-lg transition-all bg-fire-red hover:bg-red-700">
                            <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            شاهد الآن
                        </a>

                        <button
                            class="flex gap-2 items-center px-5 py-2 text-sm font-bold text-white bg-gray-700 rounded-lg hover:bg-gray-600">
                            <i class="fas fa-plus"></i>
                            أضف إلى قائمتي
                        </button>

                        <!-- زر المشاركة -->
                        <div class="inline-block relative" id="share-container">
                            <button id="copy-link"
                                class="flex gap-2 items-center px-5 py-2 text-sm text-white transition-all duration-300 hover:text-sky-400">
                                <i class="fas fa-share-alt"></i>
                                مشاركة
                            </button>

                            <!-- تنبيه النسخ -->
                            <div id="copy-alert"
                                class="absolute right-0 px-3 py-1 mt-2 text-xs text-green-500 bg-white bg-opacity-10 rounded-md shadow opacity-0 transition-opacity duration-300 pointer-events-none">
                                تم النسخ ✅
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
                        <img src="{{ asset('assets/images/logos/logo1.avif') }}" alt="logo"
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
                        <button id="watchNowBtn"
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

    <!-- Categories Filter -->
    <section class="py-6 bg-gray-800">
        <div class="container px-6 mx-auto">
            <div id="categoriesContainer" class="flex overflow-x-auto gap-4 pb-2">
                <!-- Categories will be loaded dynamically from API -->
            </div>
        </div>
    </section>

    <!-- Channels Timeline -->
    <section class="py-8">
        <div class="container px-6 mx-auto">
            <h2 class="mb-6 text-2xl font-bold">البرامج الحالية</h2>

            <!-- Time Header -->
            <div class="p-4 mb-4 bg-gray-800 rounded-lg">
                <div class="flex justify-between items-center">
                    <div class="text-lg font-bold">الوقت الحالي: <span id="currentTime" class="text-red-500"></span>
                    </div>
                    <div class="text-sm text-gray-400">جميع الأوقات بتوقيت السعودية</div>
                </div>
            </div>

            <div id="channelsContainer" class="space-y-6">
                <!-- Channels will be dynamically inserted here -->
            </div>
        </div>
    </section>

    <!-- Program Details Modal -->
    <div id="programModal" class="flex hidden fixed inset-0 z-50 justify-center items-center p-4 modal-backdrop">
        <div class="bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 id="modalTitle" class="text-2xl font-bold"></h3>
                    <button id="closeModal" class="text-2xl text-gray-400 hover:text-white">×</button>
                </div>
                <div id="modalContent" class="space-y-4">
                    <!-- Modal content will be dynamically inserted -->
                </div>
                <div class="flex gap-4 mt-6">
                    <button id="watchProgramBtn"
                        class="px-6 py-2 font-bold bg-red-600 rounded-lg transition-colors hover:bg-red-700">
                        <i class="mr-2 fas fa-play"></i>
                        شاهد البرنامج
                    </button>
                    <button class="px-6 py-2 font-bold bg-gray-700 rounded-lg transition-colors hover:bg-gray-600">
                        <i class="mr-2 fas fa-bell"></i>
                        تذكير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Stream Modal -->
    <div id="liveStreamModal" class="hidden fixed inset-0 z-50 modal-backdrop">
        <div class="relative w-full h-full bg-black">
            <button id="closeLiveStream"
                class="absolute left-10 top-16 z-10 p-2 text-white rounded-full bg-black/50 hover:bg-black/70">
                <i class="text-xl fas fa-times"></i>
            </button>
            <div class="flex justify-center items-center w-full h-full">
                <div class="text-center">
                    <div class="mx-auto mb-4 w-16 h-16 rounded-full border-t-2 border-red-500 animate-spin"></div>
                    <h3 class="mb-2 text-2xl font-bold">جاري تحميل البث المباشر...</h3>
                    <p class="text-gray-400">يرجى الانتظار</p>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('assets-site/css/live-tv.css') }}" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="{{ asset('assets-site/js/live-tv.js') }}"></script>
    @endpush
</x-front-layout>
