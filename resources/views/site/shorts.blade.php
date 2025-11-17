<x-front-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets-site/css/shorts.css') }}">
    @endpush

    <!-- Video Container -->
    <div class="overflow-y-scroll relative h-screen video-container" id="shorts-container">
        <!-- Video Item 1 -->
        @foreach ($shorts as $short)
            <div class="flex justify-center items-center h-screen video-item" data-video-id="{{ $short->id }}">
                <div class="relative bg-gray-800 rounded-lg shadow-xl backdrop-blur-md backdrop-brightness-75 shadow-white/40 video-wrapper group"
                    data-aspect="{{ $short->aspect_ratio }}">
                    <video class="object-contain w-full h-full rounded-lg cursor-pointer" loop muted playsinline
                        poster="{{ $short->poster_full_path }}" data-src="{{ $short->video_full_url }}"
                        data-video-url="{{ $short->video_full_url }}">
                    </video>

                    <!-- overlay Play/Pause -->
                    <div
                        class="flex absolute inset-0 z-30 justify-center items-center opacity-0 transition-opacity duration-300 pointer-events-none play-pause-overlay">
                        <i class="text-6xl text-white fas fa-play"></i>
                    </div>

                    <!-- شريط التحكم الجديد -->
                    <div
                        class="absolute right-0 bottom-0 left-0 z-20 px-3 py-2 bg-gradient-to-t to-transparent opacity-0 transition duration-300 video-controls from-black/70 group-hover:opacity-100">
                        <!-- الأزرار -->
                        <div class="flex justify-between items-center mt-2 text-lg text-white">
                            <!-- الشريط -->
                            <div class="relative w-full h-2 bg-gray-600 rounded-full cursor-pointer">
                                <div class="absolute top-0 left-0 w-0 h-full bg-red-500 rounded-full progress-bar">
                                </div>
                            </div>
                            <button class="px-4 volume-btn">
                                <i class="fas fa-volume-up"></i>
                            </button>
                            <button class="fullscreen-btn">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>

                    <!-- أزرار التفاعل -->
                    <div
                        class="flex absolute left-3 bottom-4 z-10 flex-col items-center transition-all duration-200 group-hover:bottom-20">
                        <a href="{{ $short->share_url }}"
                            class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 action-btn"
                            data-video="{{ $short->id }}">
                            <i class="text-xl fas fa-play"></i>
                        </a>
                        <div class="text-xs">مشاهدة</div>
                        <button
                            class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 like-btn action-btn"
                            data-video="{{ $short->id }}">
                            <i class="text-xl fas fa-heart"></i>
                        </button>
                        <div class="mb-2 text-xs like-count like-btn-{{ $short->id }}">{{ $short->likes_count }}</div>

                        <button class="action-btn comment-btn" data-video="{{ $short->id }}">
                            <i class="text-xl fas fa-comment"></i>
                        </button>
                        <div class="mb-2 text-xs comment-count comment-btn-{{ $short->id }}">{{ $short->comments_count }}</div>

                        <button class="action-btn share-btn" data-video="{{ $short->id }}" data-url="{{ $short->share_url }}">
                            <i class="text-xl fas fa-share"></i>
                        </button>
                        <div class="mb-2 text-xs share-count share-btn-{{ $short->id }}">{{ $short->shares_count }}</div>

                        <button class="action-btn save-btn" data-video="{{ $short->id }}">
                            <i class="text-xl fas fa-bookmark"></i>
                        </button>
                        <div class="text-xs">حفظ</div>
                    </div>

                    <!-- أزرار التنقل -->
                    <div
                        class="flex absolute -right-14 top-1/2 z-10 flex-col items-center transform -translate-y-1/2 navigation-arrows">
                        <button class="hidden nav-arrow prev-btn" data-direction="up">
                            <i class="text-xl fas fa-chevron-up"></i>
                        </button>
                        <button class="nav-arrow next-btn" data-direction="down">
                            <i class="text-xl fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- معلومات الفيديو -->
                    <div
                        class="absolute right-2 left-[5rem] bottom-4 text-white z-10 group-hover:bottom-24 transition-all duration-200">
                        <h3 class="text-lg font-bold">{{ $short->title }}</h3>
                        <p class="text-sm opacity-80 line-clamp-2">{{ $short->description }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- 
                <!-- Video Item 2 -->
            <div class="flex justify-center items-center h-screen video-item" data-video-id="2">
                <div class="relative bg-gray-800 rounded-lg shadow-xl backdrop-blur-md backdrop-brightness-75 video-wrapper shadow-white/40"
                    data-aspect="vertical">
                    <video class="object-contain w-full h-full rounded-lg" loop playsinline controls
                        poster="https://via.placeholder.com/800x450/374151/FFFFFF?text=Video+2"
                        data-src="./assets/videos/mov_bbb.mp4" data-video-url="https://example.com/video/2">
                        <source src="./assets/videos/mov_bbb.mp4" type="video/mp4">
                    </video>

                    <div class="flex absolute right-3 top-1/2 flex-col items-center transform -translate-y-1/2">
                        <button
                            class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 like-btn action-btn"
                            data-video="2">
                            <i class="text-xl fas fa-heart"></i>
                        </button>
                        <div class="mb-2 text-xs like-count like-btn-2">24.5K</div>


                        <button class="action-btn comment-btn" data-video="2">
                            <i class="text-xl fas fa-comment"></i>
                        </button>
                        <div class="mb-2 text-xs comment-count comment-btn-2">1.2K</div>

                        <button class="action-btn share-btn" data-video="2" data-url="https://example.com/shorts/video1">
                            <i class="text-xl fas fa-share"></i>
                        </button>
                        <div class="mb-2 text-xs share-count share-btn-2">3.7K</div>

                        <button class="action-btn watch-btn" data-video="2">
                            <i class="text-xl fas fa-bookmark"></i>
                        </button>
                        <div class="text-xs">حفظ</div>
                    </div>

                    <div
                        class="flex absolute -left-14 top-1/2 flex-col items-center transform -translate-y-1/2 navigation-arrows">
                        <button class="nav-arrow prev-btn" data-direction="up">
                            <i class="text-xl fas fa-chevron-up"></i>
                        </button>
                        <button class="nav-arrow next-btn" data-direction="down">
                            <i class="text-xl fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="absolute left-4 right-16 bottom-16 text-white">
                        <h3 class="text-lg font-bold">لحظات رياضية مثيرة</h3>
                        <p class="text-sm opacity-80 line-clamp-2">أفضل اللحظات الرياضية والحركات السريعة #رياضة #إثارة</p>
                    </div>
                </div>
            </div>

            <!-- Video Item 3 -->
            <div class="flex justify-center items-center h-screen video-item" data-video-id="3">
                <div class="relative bg-gray-800 rounded-lg video-wrapper" data-aspect="vertical">
                    <video class="object-contain w-full h-full rounded-lg" loop playsinline controls
                        poster="https://via.placeholder.com/400x600/374151/FFFFFF?text=Video+3"
                        data-src="./assets/videos/mov_bbb.mp4" data-video-url="https://example.com/video/2">
                        <source src="./assets/videos/mov_bbb.mp4" type="video/mp4">
                    </video>

                    <div class="flex absolute right-3 top-1/2 flex-col items-center transform -translate-y-1/2">
                        <button
                            class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 like-btn action-btn"
                            data-video="3">
                            <i class="text-xl fas fa-heart"></i>
                        </button>
                        <div class="mb-2 text-xs like-count like-btn-3">24.5K</div>


                        <button class="action-btn comment-btn" data-video="3">
                            <i class="text-xl fas fa-comment"></i>
                        </button>
                        <div class="mb-2 text-xs comment-count comment-btn-3">1.2K</div>

                        <button class="action-btn share-btn" data-video="3" data-url="https://example.com/shorts/video1">
                            <i class="text-xl fas fa-share"></i>
                        </button>
                        <div class="mb-2 text-xs share-count share-btn-3">3.7K</div>

                        <button class="action-btn watch-btn" data-video="3">
                            <i class="text-xl fas fa-bookmark"></i>
                        </button>
                        <div class="text-xs">حفظ</div>
                    </div>

                    <div
                        class="flex absolute -left-14 top-1/2 flex-col items-center transform -translate-y-1/2 navigation-arrows">
                        <button class="nav-arrow prev-btn" data-direction="up">
                            <i class="text-xl fas fa-chevron-up"></i>
                        </button>
                        <button class="nav-arrow next-btn" data-direction="down">
                            <i class="text-xl fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="absolute left-4 right-16 bottom-16 text-white">
                        <h3 class="text-lg font-bold">جمال الطبيعة</h3>
                        <p class="text-sm opacity-80 line-clamp-2">مناظر خلابة من الطبيعة والمناظر الجبلية #طبيعة #جمال</p>
                    </div>
                </div>
            </div>

            <!-- Video Item 4 -->
            <div class="flex justify-center items-center h-screen video-item" data-video-id="4">
                <div class="relative bg-gray-800 rounded-lg video-wrapper" data-aspect="horizontal">
                    <video class="object-contain w-full h-full rounded-lg" loop playsinline controls
                        poster="https://via.placeholder.com/800x450/374151/FFFFFF?text=Video+4"
                        data-src="./assets/videos/short-1.mp4" data-video-url="https://example.com/video/2">
                        <source src="./assets/videos/short-1.mp4" type="video/mp4">
                    </video>

                    <div class="flex absolute right-3 top-1/2 flex-col items-center transform -translate-y-1/2">
                        <button
                            class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 like-btn action-btn"
                            data-video="4">
                            <i class="text-xl fas fa-heart"></i>
                        </button>
                        <div class="mb-2 text-xs like-count like-btn-4">24.5K</div>


                        <button class="action-btn comment-btn" data-video="4">
                            <i class="text-xl fas fa-comment"></i>
                        </button>
                        <div class="mb-2 text-xs comment-count comment-btn-4">1.2K</div>

                        <button class="action-btn share-btn" data-video="4" data-url="https://example.com/shorts/video1">
                            <i class="text-xl fas fa-share"></i>
                        </button>
                        <div class="mb-2 text-xs share-count share-btn-4">3.7K</div>

                        <button class="action-btn watch-btn" data-video="4">
                            <i class="text-xl fas fa-bookmark"></i>
                        </button>
                        <div class="text-xs">حفظ</div>
                    </div>

                    <div
                        class="flex absolute -left-14 top-1/2 flex-col items-center transform -translate-y-1/2 navigation-arrows">
                        <button class="nav-arrow prev-btn" data-direction="up">
                            <i class="text-xl fas fa-chevron-up"></i>
                        </button>
                        <button class="nav-arrow next-btn" data-direction="down">
                            <i class="text-xl fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="absolute left-4 right-16 bottom-16 text-white">
                        <h3 class="text-lg font-bold">تقنيات حديثة</h3>
                        <p class="text-sm opacity-80 line-clamp-2">عرض لأحدث التقنيات والابتكارات في عالم التكنولوجيا
                            #تقنية
                            #ابتكار</p>
                    </div>
                </div>
            </div>

            <!-- Video Item 5 -->
            <div class="flex justify-center items-center h-screen video-item" data-video-id="5">
                <div class="relative bg-gray-800 rounded-lg video-wrapper" data-aspect="vertical">
                    <video class="object-contain w-full h-full rounded-lg" loop playsinline controls
                        poster="https://via.placeholder.com/400x600/374151/FFFFFF?text=Video+5"
                        data-src="./assets/videos/mov_bbb.mp4" data-video-url="https://example.com/video/2">
                        <source src="./assets/videos/mov_bbb.mp4" type="video/mp4">
                    </video>
                    <div class="flex absolute right-3 top-1/2 flex-col items-center transform -translate-y-1/2">
                        <button
                            class="flex flex-col justify-center items-center w-12 h-12 rounded-full transition-all duration-200 like-btn action-btn"
                            data-video="5">
                            <i class="text-xl fas fa-heart"></i>
                        </button>
                        <div class="mb-2 text-xs like-count like-btn-5">24.5K</div>


                        <button class="action-btn comment-btn" data-video="5">
                            <i class="text-xl fas fa-comment"></i>
                        </button>
                        <div class="mb-2 text-xs comment-count comment-btn-5">1.2K</div>

                        <button class="action-btn share-btn" data-video="5" data-url="https://example.com/shorts/video1">
                            <i class="text-xl fas fa-share"></i>
                        </button>
                        <div class="mb-2 text-xs share-count share-btn-5">3.7K</div>

                        <button class="action-btn watch-btn" data-video="5">
                            <i class="text-xl fas fa-bookmark"></i>
                        </button>
                        <div class="text-xs">حفظ</div>
                    </div>

                    <div
                        class="flex absolute -left-14 top-1/2 flex-col items-center transform -translate-y-1/2 navigation-arrows">
                        <button class="nav-arrow prev-btn" data-direction="up">
                            <i class="text-xl fas fa-chevron-up"></i>
                        </button>
                        <button class="hidden nav-arrow next-btn" data-direction="down">
                            <i class="text-xl fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="absolute left-4 right-16 bottom-16 text-white">
                        <h3 class="text-lg font-bold">فنون وإبداع</h3>
                        <p class="text-sm opacity-80 line-clamp-2">أعمال فنية مبدعة ولوحات رائعة #فن #إبداع #لوحات</p>
                    </div>
                </div>
            </div> 
        --}}
    </div>

    <!-- Share Popup Modal -->
    <div class="flex hidden fixed inset-0 z-50 justify-center items-center p-4 bg-black bg-opacity-50 share-overlay">
        <div class="relative p-6 w-full max-w-md bg-gray-900 rounded-2xl share-popup">
            <!-- Close Button -->
            <button class="absolute top-4 right-4 text-gray-400 close-share hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Title -->
            <h3 class="mb-6 text-xl font-bold text-center">Share Video</h3>

            <!-- URL Input -->
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-300">Video Link</label>
                <div class="flex items-center space-x-2">
                    <input type="text" id="share-url"
                        class="flex-1 px-4 py-3 text-white bg-gray-800 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        readonly>
                    <button id="copy-link"
                        class="px-4 py-3 font-medium text-white bg-blue-600 rounded-lg transition-colors duration-200 hover:bg-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Social Share Icons -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <!-- WhatsApp -->
                <button
                    class="flex flex-col items-center p-4 bg-gray-800 rounded-xl transition-colors duration-200 social-share-btn hover:bg-gray-700"
                    data-platform="whatsapp">
                    <div class="flex justify-center items-center mb-2 w-12 h-12 bg-green-500 rounded-full">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-300">WhatsApp</span>
                </button>

                <!-- Facebook -->
                <button
                    class="flex flex-col items-center p-4 bg-gray-800 rounded-xl transition-colors duration-200 social-share-btn hover:bg-gray-700"
                    data-platform="facebook">
                    <div class="flex justify-center items-center mb-2 w-12 h-12 bg-blue-600 rounded-full">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-300">Facebook</span>
                </button>

                <!-- Twitter -->
                <button
                    class="flex flex-col items-center p-4 bg-gray-800 rounded-xl transition-colors duration-200 social-share-btn hover:bg-gray-700"
                    data-platform="twitter">
                    <div class="flex justify-center items-center mb-2 w-12 h-12 bg-blue-400 rounded-full">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-300">Twitter</span>
                </button>

                <!-- Telegram -->
                <button
                    class="flex flex-col items-center p-4 bg-gray-800 rounded-xl transition-colors duration-200 social-share-btn hover:bg-gray-700"
                    data-platform="telegram">
                    <div class="flex justify-center items-center mb-2 w-12 h-12 bg-blue-500 rounded-full">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-300">Telegram</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Comment Panel -->
    <div class="flex fixed top-0 right-0 z-[9999] flex-col w-full h-full bg-gray-900 comment-panel md:w-96">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold">Comments</h3>
            <button class="text-gray-400 close-comments hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Comments Container -->
        <div class="overflow-y-auto flex-1 p-4 space-y-4 comments-container" dir="ltr">
            <!-- Sample Comments will be loaded here -->
        </div>

        <!-- Comment Input -->
        <div class="p-4 border-t border-gray-700" dir="ltr">
            <div class="flex items-center space-x-3">
                <div
                    class="flex justify-center items-center w-8 h-8 text-sm font-bold bg-gradient-to-r from-purple-500 to-pink-500 rounded-full">
                    U
                </div>
                <div class="relative flex-1">
                    <input type="text"
                        class="px-4 py-2 w-full placeholder-gray-400 text-white bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Add a comment..." id="comment-input">
                    <button
                        class="absolute right-2 top-1/2 text-blue-500 transform -translate-y-1/2 hover:text-blue-400"
                        id="send-comment">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- الخلفية السوداء -->
    <div id="profileOverlay" class="hidden fixed inset-0 z-40 bg-black/60"></div>

    <!-- Overlay & Popup -->
    <div id="activityPopup" class="flex hidden fixed inset-0 z-50 justify-center items-center bg-black bg-opacity-60">
        <div class="relative w-[90%] max-w-xl bg-[#0f172a] text-white rounded-md overflow-hidden shadow-xl">
            <!-- Header -->
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-600">
                <h2 class="text-xl font-bold">نشاطاتي</h2>
                <button class="text-xl text-gray-400 close-activity hover:text-white">&times;</button>
            </div>

            <!-- Tabs -->
            <div class="flex justify-around text-sm font-medium border-b border-gray-700">
                <button class="px-4 py-2 w-full border-b-2 border-blue-500 tab-link hover:bg-gray-800 active-tab"
                    data-tab="likes">إعجابات <span class="text-xs text-gray-400">12</span></button>
                <button class="px-4 py-2 w-full tab-link hover:bg-gray-800" data-tab="comments">تعليقات <span
                        class="text-xs text-gray-400">12</span></button>
                <button class="px-4 py-2 w-full tab-link hover:bg-gray-800" data-tab="shares">مشاركة <span
                        class="text-xs text-gray-400">12</span></button>
            </div>

            <!-- المحتوى -->
            <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- إعجابات -->
                <div class="tab-content" id="likes">
                    <div
                        class="flex justify-between items-center p-3 bg-gray-800 rounded transition hover:bg-gray-700">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <img src="https://placehold.co/60x80" onerror="this.src='default.jpg'"
                                class="object-cover w-16 h-20 rounded-md">
                            <div>
                                <h3 class="text-base font-semibold">فيديو مثير</h3>
                                <p class="text-xs text-gray-400">#حركة #كوميديا</p>
                            </div>
                        </div>
                        <a href="#" class="text-blue-400 hover:underline">مشاهدة</a>
                    </div>
                </div>

                <!-- تعليقات -->
                <div class="hidden tab-content" id="comments">
                    <div
                        class="flex justify-between items-center p-3 bg-gray-800 rounded transition hover:bg-gray-700">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <img src="https://placehold.co/60x80" class="object-cover w-16 h-20 rounded-md">
                            <div>
                                <h3 class="text-base font-semibold">فيديو مشوق</h3>
                                <p class="text-xs text-gray-400">تعليقي: هذا المقطع رهيب جدًا 🔥</p>
                            </div>
                        </div>
                        <a href="#" class="text-blue-400 hover:underline">مشاهدة</a>
                    </div>
                </div>

                <!-- مشاركة -->
                <div class="hidden tab-content" id="shares">
                    <div
                        class="flex justify-between items-center p-3 bg-gray-800 rounded transition hover:bg-gray-700">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <img src="https://placehold.co/60x80" class="object-cover w-16 h-20 rounded-md">
                            <div>
                                <h3 class="text-base font-semibold">فيديو محفوظ</h3>
                                <p class="text-xs text-gray-400">#تحفيز #ذكاء</p>
                            </div>
                        </div>
                        <a href="#" class="text-blue-400 hover:underline">مشاهدة</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="hidden fixed inset-0 z-40 bg-black bg-opacity-50 comment-overlay"></div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/+esm" type="module"></script>
        <script src="{{ asset('assets-site/js/shorts.js') }}" type="module"></script>
        <script>
            const contents = [];
        </script>
        <script>
            $(document).ready(function() {
                const video = $('video')[0];
                const $overlay = $('.play-pause-overlay');
                const $progress = $('.progress-bar');
                const $wrapper = $('.video-wrapper');

                // تشغيل/إيقاف عند الضغط
                $(video).on('click', function() {
                    if (video.paused) {
                        video.play();
                        showOverlay('fa-play');
                    } else {
                        video.pause();
                        showOverlay('fa-pause');
                    }
                });

                // عرض الأيقونة لحظياً
                function showOverlay(iconClass) {
                    $overlay.find('i')
                        .removeClass('fa-play fa-pause')
                        .addClass(iconClass);
                    $overlay
                        .css('opacity', 1)
                        .stop(true, true).fadeIn(100).delay(600).fadeOut(300);
                }

                // تحديث الشريط
                video.addEventListener('timeupdate', () => {
                    const percent = (video.currentTime / video.duration) * 100;
                    $progress.css('width', percent + '%');
                });

                // تحريك رأس الشريط بالضغط
                $('.video-controls .relative').on('click', function(e) {
                    const offset = $(this).offset().left;
                    const width = $(this).width();
                    const x = e.pageX - offset;
                    const percent = x / width;
                    video.currentTime = percent * video.duration;
                });

                // زر الفول سكرين
                $('.fullscreen-btn').on('click', () => {
                    if (!document.fullscreenElement) {
                        $wrapper[0].requestFullscreen();
                    } else {
                        document.exitFullscreen();
                    }
                });

                // زر الصوت
                $('.volume-btn').on('click', function() {
                    video.muted = !video.muted;
                    const icon = $(this).find('i');
                    if (video.muted) {
                        icon.removeClass('fa-volume-up').addClass('fa-volume-mute');
                    } else {
                        icon.removeClass('fa-volume-mute').addClass('fa-volume-up');
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                // فتح البوب أب
                $('.open-activity').click(function() {
                    $('#activityPopup').removeClass('hidden');
                    $('body').addClass('overflow-hidden');
                });

                // إغلاق البوب أب
                $('.close-activity, #activityPopup').click(function(e) {
                    if (e.target.id === 'activityPopup' || $(e.target).hasClass('close-activity')) {
                        $('#activityPopup').addClass('hidden');
                        $('body').removeClass('overflow-hidden');
                    }
                });

                // التبويبات
                $('.tab-link').click(function() {
                    const tab = $(this).data('tab');

                    $('.tab-link').removeClass('active-tab').removeClass('border-b-2 border-blue-500');
                    $(this).addClass('active-tab border-b-2 border-blue-500');

                    $('.tab-content').addClass('hidden');
                    $('#' + tab).removeClass('hidden');
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                const overlay = $('.comment-overlay');
                const menu = $('#user-avatar-menu, #user-avatar-menu-profile');

                menu.hover(
                    function() {
                        // عند الدخول
                        overlay.removeClass('hidden');
                    },
                    function() {
                        // عند الخروج
                        overlay.addClass('hidden');
                    }
                );

                // إخفاء عند الضغط على الـ overlay نفسه
                overlay.click(function() {
                    overlay.addClass('hidden');
                });
            });
        </script>
    @endpush
</x-front-layout>
