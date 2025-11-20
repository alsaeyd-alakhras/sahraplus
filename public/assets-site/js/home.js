/**
 * Home Page JS - Continue Watching Section
 * Dynamically loads and displays continue watching content
 */

(function($) {
    'use strict';

    // Only run on home page
    if (!$('body').hasClass('home-page') && !$('#continue-watching-section').length) {
        return;
    }

    // Load continue watching if user is authenticated and has active profile
    if (window.authUser && window.activeProfileId) {
        loadContinueWatching();
    }

    function loadContinueWatching() {
        if (typeof WatchProgressManager === 'undefined') {
            console.log('WatchProgressManager not available');
            return;
        }

        WatchProgressManager.getContinueWatching(window.activeProfileId)
            .then(response => {
                const data = response.data || response;
                
                if (!data || data.length === 0) {
                    // Hide continue watching section if empty
                    $('#continue-watching-section').hide();
                    return;
                }

                renderContinueWatching(data);
            })
            .catch(error => {
                console.error('Failed to load continue watching:', error);
                $('#continue-watching-section').hide();
            });
    }

    function renderContinueWatching(items) {
        const $container = $('#continue-watching-container .swiper-wrapper');
        
        if (!$container.length) {
            console.warn('Continue watching container not found');
            return;
        }

        // Clear existing content (except server-rendered items if any)
        // We'll append to existing items for now
        
        items.forEach(item => {
            const card = createContinueWatchingCard(item);
            $container.append(card);
        });

        // Reinitialize swiper if needed
        if (typeof Swiper !== 'undefined') {
            // The swiper should already be initialized by script.js
            // Just update it
            const swiperInstance = $container.closest('.swiper')[0]?.swiper;
            if (swiperInstance) {
                swiperInstance.update();
            }
        }

        // Show the section
        $('#continue-watching-section').show();
    }

    function createContinueWatchingCard(item) {
        const progress = item.progress || 0;
        const title = item.title || 'بدون عنوان';
        const url = getContentUrl(item);

        return `
            <div class="swiper-slide">
                <div class="movie-slider-card">
                    <div class="relative">
                        <div class="object-cover w-full rounded-md aspect-video bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                        </div>
                        <!-- شريط المدة -->
                        <div class="absolute bottom-0 left-0 h-1 bg-teal-400" style="width: ${progress}%"></div>
                        <!-- زر التشغيل فوق الصورة -->
                        <a href="${url}" class="flex absolute inset-0 justify-end items-end p-4 gap-[8px]">
                            <span class="relative flex shrink-0 items-center justify-center text-shahidGray h-[24px] w-[24px]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="absolute inset-0 w-full h-full">
                                    <defs>
                                        <linearGradient id="linearGradient-${item.id}" x1="0%" x2="100%" y1="50%" y2="50%">
                                            <stop offset="0%" stop-color="#0C9"></stop>
                                            <stop offset="100%" stop-color="#09F"></stop>
                                        </linearGradient>
                                    </defs>
                                    <g fill="none" fill-opacity="0.4" fill-rule="evenodd" stroke="none" stroke-width="1">
                                        <rect width="31" height="31" x="0.5" y="0.5" fill="#181D25" stroke="url(#linearGradient-${item.id})" rx="15.5"></rect>
                                    </g>
                                </svg>
                                <img alt="playIcon" title="لعب" class="relative h-[16px] w-[16px]" src="https://shahid.mbc.net/staticFiles/production/static/images/shdicons-24-2-px-player-play-filled.svg">
                            </span>
                        </a>
                    </div>
                    <div class="movie-slider-details">
                        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                            ${title}
                        </div>
                        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                            <div class="flex gap-x-4">
                                <!-- زر: المزيد من المعلومات -->
                                <a href="${url}" class="flex gap-1 items-center transition-all duration-200 group hover:text-white">
                                    <span class="flex relative justify-center items-center w-6 h-6 rounded-full transition-transform duration-300 shrink-0 text-shahidGray bg-white/5 group-hover:rotate-12 group-hover:scale-110">
                                        <i class="fa-solid fa-info"></i>
                                    </span>
                                    <span class="text-xs truncate transition-colors duration-200 text-shahidGray group-hover:text-white">
                                        المزيد من المعلومات
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getContentUrl(item) {
        // Assuming item has id and type or we can infer from structure
        const locale = document.documentElement.lang || 'ar';
        const baseUrl = `/${locale}`;
        
        // You might need to adjust this based on actual API response structure
        if (item.type === 'movie' || !item.type) {
            return `${baseUrl}/movies/${item.id}`;
        } else if (item.type === 'series') {
            return `${baseUrl}/series/${item.id}`;
        } else if (item.type === 'episode') {
            return `${baseUrl}/series/episode/${item.id}`;
        }
        
        return '#';
    }

})(jQuery);

