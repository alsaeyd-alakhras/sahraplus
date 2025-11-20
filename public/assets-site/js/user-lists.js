/**
 * User Lists JS - Watchlist, Favorites, History Pages
 * Handles rendering and interaction for user content lists
 */

(function($) {
    'use strict';

    // ============================================
    // Watchlist Page
    // ============================================

    if ($('#watchlist-container').length) {
        loadWatchlist();
    }

    function loadWatchlist() {
        const $container = $('#watchlist-container');
        const $loading = $('#watchlist-loading');
        const $empty = $('#watchlist-empty');

        if ($loading) $loading.removeClass('hidden');

        WatchlistManager.getAll(50).then(response => {
            if ($loading) $loading.addClass('hidden');

            const data = response.data || response;
            
            if (!data || data.length === 0) {
                if ($empty) $empty.removeClass('hidden');
                return;
            }

            $container.empty();

            data.forEach(item => {
                const card = createWatchlistCard(item);
                $container.append(card);
            });

        }).catch(error => {
            if ($loading) $loading.addClass('hidden');
            console.error('Failed to load watchlist:', error);
            showToast('فشل تحميل قائمة المشاهدة', 'error');
        });
    }

    function createWatchlistCard(item) {
        const typeText = item.type === 'movie' ? 'فيلم' : (item.type === 'series' ? 'مسلسل' : 'حلقة');
        const url = getContentUrl(item.type, item.content_id);

        return `
            <div class="overflow-hidden bg-gray-800 rounded-lg movie-card" data-watchlist-item="${item.content_id}">
                <a href="${url}">
                    <div class="relative">
                        <div class="w-full aspect-[2/3] bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-film text-4xl text-gray-600"></i>
                        </div>
                    </div>
                    <div class="p-3">
                        <h3 class="mb-1 text-sm font-bold text-white line-clamp-2">${item.title || 'بدون عنوان'}</h3>
                        <div class="mb-2 text-xs text-gray-400">
                            <span>${typeText}</span>
                            ${item.added_at ? `<span class="mx-1">•</span><span>${item.added_at}</span>` : ''}
                        </div>
                    </div>
                </a>
                <div class="px-3 pb-3">
                    <button class="remove-from-watchlist w-full px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700 transition-all"
                        data-content-id="${item.content_id}"
                        data-content-type="${item.type}">
                        <i class="fas fa-trash-alt mr-1"></i>
                        إزالة من القائمة
                    </button>
                </div>
            </div>
        `;
    }

    // Handle remove from watchlist
    $(document).on('click', '.remove-from-watchlist', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const contentId = $btn.data('content-id');
        const $card = $btn.closest('[data-watchlist-item]');

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري الحذف...');

        WatchlistManager.remove(contentId).then(response => {
            showToast('تم الحذف من قائمة المشاهدة', 'success');
            $card.fadeOut(300, function() {
                $(this).remove();
                
                // Check if empty
                if ($('#watchlist-container').children().length === 0) {
                    $('#watchlist-empty').removeClass('hidden');
                }
            });
        }).catch(error => {
            console.error('Failed to remove from watchlist:', error);
            showToast('فشل في حذف العنصر', 'error');
            $btn.prop('disabled', false).html('<i class="fas fa-trash-alt mr-1"></i> إزالة من القائمة');
        });
    });

    // ============================================
    // Favorites Page
    // ============================================

    if ($('#favorites-container').length) {
        loadFavorites();
    }

    function loadFavorites() {
        const $container = $('#favorites-container');
        const $loading = $('#favorites-loading');
        const $empty = $('#favorites-empty');

        if ($loading) $loading.removeClass('hidden');

        FavoritesManager.getAll().then(response => {
            if ($loading) $loading.addClass('hidden');

            const data = response.data || response;
            
            if (!data || data.length === 0) {
                if ($empty) $empty.removeClass('hidden');
                return;
            }

            $container.empty();

            data.forEach(item => {
                const card = createFavoriteCard(item);
                $container.append(card);
            });

        }).catch(error => {
            if ($loading) $loading.addClass('hidden');
            console.error('Failed to load favorites:', error);
            showToast('فشل تحميل المفضلة', 'error');
        });
    }

    function createFavoriteCard(item) {
        const typeText = item.type === 'movie' ? 'فيلم' : (item.type === 'series' ? 'مسلسل' : 'حلقة');
        const url = getContentUrl(item.type, item.content_id);

        return `
            <div class="overflow-hidden bg-gray-800 rounded-lg movie-card" data-favorite-item="${item.content_id}">
                <a href="${url}">
                    <div class="relative">
                        <div class="w-full aspect-[2/3] bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-heart text-4xl text-red-500"></i>
                        </div>
                    </div>
                    <div class="p-3">
                        <h3 class="mb-1 text-sm font-bold text-white line-clamp-2">${item.title || 'بدون عنوان'}</h3>
                        <div class="mb-2 text-xs text-gray-400">
                            <span>${typeText}</span>
                        </div>
                    </div>
                </a>
                <div class="px-3 pb-3">
                    <button class="remove-from-favorites w-full px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700 transition-all"
                        data-content-id="${item.content_id}"
                        data-content-type="${item.type}">
                        <i class="fas fa-heart-broken mr-1"></i>
                        إزالة من المفضلة
                    </button>
                </div>
            </div>
        `;
    }

    // Handle remove from favorites
    $(document).on('click', '.remove-from-favorites', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const contentId = $btn.data('content-id');
        const contentType = $btn.data('content-type');
        const $card = $btn.closest('[data-favorite-item]');

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري الحذف...');

        FavoritesManager.toggle(window.activeProfileId, contentType, contentId).then(response => {
            showToast('تم الحذف من المفضلة', 'success');
            $card.fadeOut(300, function() {
                $(this).remove();
                
                // Check if empty
                if ($('#favorites-container').children().length === 0) {
                    $('#favorites-empty').removeClass('hidden');
                }
            });
        }).catch(error => {
            console.error('Failed to remove from favorites:', error);
            showToast('فشل في حذف العنصر', 'error');
            $btn.prop('disabled', false).html('<i class="fas fa-heart-broken mr-1"></i> إزالة من المفضلة');
        });
    });

    // ============================================
    // Viewing History Page
    // ============================================

    if ($('#history-container').length) {
        loadHistory();
    }

    function loadHistory() {
        const $container = $('#history-container');
        const $loading = $('#history-loading');
        const $empty = $('#history-empty');

        if ($loading) $loading.removeClass('hidden');

        ViewingHistoryManager.getHistory().then(response => {
            if ($loading) $loading.addClass('hidden');

            const data = response.data || response;
            
            if (!data || data.length === 0) {
                if ($empty) $empty.removeClass('hidden');
                return;
            }

            $container.empty();

            data.forEach(item => {
                const card = createHistoryCard(item);
                $container.append(card);
            });

        }).catch(error => {
            if ($loading) $loading.addClass('hidden');
            console.error('Failed to load history:', error);
            showToast('فشل تحميل سجل المشاهدة', 'error');
        });
    }

    function createHistoryCard(item) {
        const typeText = item.type === 'movie' ? 'فيلم' : (item.type === 'series' ? 'مسلسل' : 'حلقة');
        const url = getContentUrl(item.type, item.id);
        const date = new Date(item.at);
        const formattedDate = date.toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        return `
            <div class="overflow-hidden bg-gray-800 rounded-lg movie-card">
                <a href="${url}">
                    <div class="relative">
                        <div class="w-full aspect-[2/3] bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-history text-4xl text-gray-600"></i>
                        </div>
                    </div>
                    <div class="p-3">
                        <h3 class="mb-1 text-sm font-bold text-white line-clamp-2">${item.title || 'بدون عنوان'}</h3>
                        <div class="mb-2 text-xs text-gray-400">
                            <span>${typeText}</span>
                            <span class="mx-1">•</span>
                            <span>${formattedDate}</span>
                        </div>
                    </div>
                </a>
            </div>
        `;
    }

    // ============================================
    // Helper Functions
    // ============================================

    function getContentUrl(type, id) {
        const locale = document.documentElement.lang || 'ar';
        const baseUrl = `/${locale}`;
        
        switch(type) {
            case 'movie':
                return `${baseUrl}/movies/${id}`;
            case 'series':
                return `${baseUrl}/series/${id}`;
            case 'episode':
                return `${baseUrl}/series/episode/${id}`;
            default:
                return '#';
        }
    }

})(jQuery);

