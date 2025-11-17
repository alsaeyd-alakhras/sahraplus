/**
 * Content Interactions JS - Phase 3
 * Handles Watchlists, Favorites, Ratings, Watch Progress, and Viewing History
 * Uses jQuery + AJAX to interact with API v1
 */

(function($) {
    'use strict';

    // ============================================
    // Helper Functions
    // ============================================

    /**
     * Generic API request helper using jQuery AJAX
     */
    window.apiRequest = function(options) {
        const defaults = {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            dataType: 'json'
        };

        const settings = $.extend({}, defaults, options);

        return $.ajax({
            url: settings.url,
            method: settings.method,
            headers: settings.headers,
            data: settings.data,
            dataType: settings.dataType,
            contentType: settings.contentType || 'application/json; charset=utf-8',
            processData: settings.processData !== undefined ? settings.processData : (settings.method === 'GET')
        });
    };

    /**
     * Show notification using Toastr
     */
    window.showToast = function(message, type = 'success') {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    };

    /**
     * Check if user is authenticated
     */
    function requireAuth() {
        if (!window.authUser) {
            showToast('يرجى تسجيل الدخول أولاً', 'warning');
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
            return false;
        }
        return true;
    }

    /**
     * Check if profile is selected
     */
    function requireProfile() {
        if (!window.activeProfileId) {
            showToast('يرجى اختيار بروفايل أولاً', 'warning');
            return false;
        }
        return true;
    }

    /**
     * Format time from seconds to HH:MM:SS
     */
    function formatTime(seconds) {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);
        return `${h > 0 ? h + ':' : ''}${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }

    /**
     * Format date to Arabic
     */
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // ============================================
    // Watchlist Functions
    // ============================================

    window.WatchlistManager = {
        /**
         * Check watchlist status for content
         */
        checkStatus: function(type, contentId) {
            if (!requireAuth() || !requireProfile()) {
                return Promise.resolve({ exists: false });
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/${type}/${contentId}/watchlist/status`,
                method: 'GET'
            }).then(response => {
                return { exists: response.success || false };
            }).catch(() => {
                return { exists: false };
            });
        },

        /**
         * Add to watchlist
         */
        add: function(profileId, contentType, contentId) {
            return apiRequest({
                url: `${window.apiBaseUrl}/watchlist/store`,
                method: 'POST',
                data: JSON.stringify({
                    profile_id: profileId,
                    content_type: contentType,
                    content_id: contentId
                })
            });
        },

        /**
         * Remove from watchlist
         */
        remove: function(contentId) {
            return apiRequest({
                url: `${window.apiBaseUrl}/${contentId}/watchlist/delete`,
                method: 'DELETE'
            });
        },

        /**
         * Get all watchlist items
         */
        getAll: function(perPage = 20) {
            if (!requireAuth()) {
                return Promise.reject('Not authenticated');
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/watchlists?per_page=${perPage}`,
                method: 'GET'
            });
        },

        /**
         * Initialize watchlist button on content pages
         */
        initButton: function(buttonSelector) {
            const $btn = $(buttonSelector);
            if (!$btn.length) return;

            const contentId = $btn.data('content-id');
            const contentType = $btn.data('content-type');

            if (!contentId || !contentType) {
                console.warn('Watchlist button missing data attributes');
                return;
            }

            // Check initial status
            this.checkStatus(contentType, contentId).then(result => {
                if (result.exists) {
                    this.updateButtonState($btn, true);
                }
            });

            // Handle click
            $btn.off('click.watchlist').on('click.watchlist', (e) => {
                e.preventDefault();
                this.toggleWatchlist($btn);
            });
        },

        /**
         * Toggle watchlist status
         */
        toggleWatchlist: function($btn) {
            if (!requireAuth() || !requireProfile()) return;

            const contentId = $btn.data('content-id');
            const contentType = $btn.data('content-type');
            const inWatchlist = $btn.data('in-watchlist') === true;
            const $icon = $btn.find('i');
            const $text = $btn.find('span');

            // Disable button during request
            $btn.prop('disabled', true);
            $icon.removeClass().addClass('fas fa-spinner fa-spin');

            const promise = inWatchlist 
                ? this.remove(contentId)
                : this.add(window.activeProfileId, contentType, contentId);

            promise.then(response => {
                if (response.success || response.status) {
                    this.updateButtonState($btn, !inWatchlist);
                    showToast(response.message || (inWatchlist ? 'تم الحذف من قائمة المشاهدة' : 'تم الإضافة لقائمة المشاهدة'), 'success');
                }
            }).catch(error => {
                console.error('Watchlist error:', error);
                const message = error.responseJSON?.message || 'حدث خطأ في تحديث قائمة المشاهدة';
                showToast(message, 'error');
                // Restore icon
                $icon.removeClass().addClass('fas fa-plus');
            }).always(() => {
                $btn.prop('disabled', false);
            });
        },

        /**
         * Update button UI state
         */
        updateButtonState: function($btn, inWatchlist) {
            const $icon = $btn.find('i');
            const $text = $btn.find('span');

            $btn.data('in-watchlist', inWatchlist);

            if (inWatchlist) {
                $icon.removeClass().addClass('fas fa-check');
                $text.text('في القائمة');
                $btn.removeClass('bg-gray-700 hover:bg-gray-600')
                    .addClass('bg-green-600 hover:bg-green-700');
            } else {
                $icon.removeClass().addClass('fas fa-plus');
                $text.text('أضف للمشاهدة');
                $btn.removeClass('bg-green-600 hover:bg-green-700')
                    .addClass('bg-gray-700 hover:bg-gray-600');
            }
        }
    };

    // ============================================
    // Favorites Functions
    // ============================================

    window.FavoritesManager = {
        /**
         * Check favorite status
         */
        checkStatus: function(type, contentId) {
            if (!requireAuth() || !requireProfile()) {
                return Promise.resolve({ exists: false });
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/${type}/${contentId}/favorite/status`,
                method: 'GET'
            }).then(response => {
                return { exists: response.exists || false };
            }).catch(() => {
                return { exists: false };
            });
        },

        /**
         * Toggle favorite
         */
        toggle: function(profileId, type, contentId) {
            return apiRequest({
                url: `${window.apiBaseUrl}/favorite/toggle/${type}/${contentId}`,
                method: 'POST',
                data: JSON.stringify({
                    profile_id: profileId
                })
            });
        },

        /**
         * Get all favorites
         */
        getAll: function() {
            if (!requireAuth()) {
                return Promise.reject('Not authenticated');
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/favorites`,
                method: 'GET'
            });
        },

        /**
         * Initialize favorite button
         */
        initButton: function(buttonSelector) {
            const $btn = $(buttonSelector);
            if (!$btn.length) return;

            const contentId = $btn.data('content-id');
            const contentType = $btn.data('content-type');

            if (!contentId || !contentType) {
                console.warn('Favorite button missing data attributes');
                return;
            }

            // Check initial status
            this.checkStatus(contentType, contentId).then(result => {
                if (result.exists) {
                    this.updateButtonState($btn, true);
                }
            });

            // Handle click
            $btn.off('click.favorite').on('click.favorite', (e) => {
                e.preventDefault();
                this.toggleFavorite($btn);
            });
        },

        /**
         * Toggle favorite
         */
        toggleFavorite: function($btn) {
            if (!requireAuth() || !requireProfile()) return;

            const contentId = $btn.data('content-id');
            const contentType = $btn.data('content-type');
            const isFavorite = $btn.data('is-favorite') === true;
            const $icon = $btn.find('i');

            $btn.prop('disabled', true);
            $icon.removeClass().addClass('fas fa-spinner fa-spin');

            this.toggle(window.activeProfileId, contentType, contentId)
                .then(response => {
                    if (response.success || response.status) {
                        this.updateButtonState($btn, !isFavorite);
                        showToast(response.message || (isFavorite ? 'تم الحذف من المفضلة' : 'تم الإضافة للمفضلة'), 'success');
                    }
                }).catch(error => {
                    console.error('Favorite error:', error);
                    const message = error.responseJSON?.message || 'حدث خطأ في تحديث المفضلة';
                    showToast(message, 'error');
                    $icon.removeClass().addClass('fas fa-heart');
                }).always(() => {
                    $btn.prop('disabled', false);
                });
        },

        /**
         * Update button state
         */
        updateButtonState: function($btn, isFavorite) {
            const $icon = $btn.find('i');
            $btn.data('is-favorite', isFavorite);

            if (isFavorite) {
                $icon.removeClass('far').addClass('fas fa-heart text-red-500');
            } else {
                $icon.removeClass('fas text-red-500').addClass('far fa-heart');
            }
        }
    };

    // ============================================
    // Watch Progress Functions
    // ============================================

    window.WatchProgressManager = {
        /**
         * Get progress for content
         */
        getProgress: function(type, contentId) {
            if (!requireAuth() || !requireProfile()) {
                return Promise.resolve(null);
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/progress/${type}/${contentId}`,
                method: 'GET'
            }).then(response => {
                if (response.success || response.data) {
                    return response.data || response;
                }
                return null;
            }).catch(() => {
                return null;
            });
        },

        /**
         * Update progress
         */
        updateProgress: function(type, contentId, watchedSeconds, totalSeconds) {
            if (!requireAuth() || !requireProfile()) return Promise.resolve();

            return apiRequest({
                url: `${window.apiBaseUrl}/watch-progress-update/${type}/${contentId}`,
                method: 'PUT',
                data: JSON.stringify({
                    profile_id: window.activeProfileId,
                    watched_seconds: Math.floor(watchedSeconds),
                    total_seconds: Math.floor(totalSeconds)
                })
            });
        },

        /**
         * Get continue watching list
         */
        getContinueWatching: function(profileId) {
            if (!requireAuth()) {
                return Promise.reject('Not authenticated');
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/watch-progress-profiles/${profileId}/continue-watching`,
                method: 'GET'
            });
        }
    };

    // ============================================
    // Viewing History Functions
    // ============================================

    window.ViewingHistoryManager = {
        /**
         * Get viewing history
         */
        getHistory: function(contentType = null) {
            if (!requireAuth()) {
                return Promise.reject('Not authenticated');
            }

            let url = `${window.apiBaseUrl}/history`;
            if (contentType) {
                url += `?content_type=${contentType}`;
            }

            return apiRequest({
                url: url,
                method: 'GET'
            });
        }
    };

    // ============================================
    // User Ratings Functions
    // ============================================

    window.RatingsManager = {
        /**
         * Get ratings for content
         */
        getRatings: function(type, contentId) {
            if (!requireAuth()) {
                return Promise.resolve(null);
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/ratings/${type}/${contentId}`,
                method: 'GET'
            }).then(response => {
                if (response.success || response.data) {
                    return response.data || response;
                }
                return null;
            }).catch(() => {
                return null;
            });
        },

        /**
         * Submit rating
         */
        submitRating: function(type, contentId, rating, review = '', isSpoiler = false) {
            if (!requireAuth() || !requireProfile()) {
                return Promise.reject('Authentication required');
            }

            return apiRequest({
                url: `${window.apiBaseUrl}/rating-store/${type}/${contentId}`,
                method: 'POST',
                data: JSON.stringify({
                    profile_id: window.activeProfileId,
                    rating: rating,
                    review: review,
                    is_spoiler: isSpoiler
                })
            });
        },

        /**
         * Delete rating
         */
        deleteRating: function(ratingId) {
            return apiRequest({
                url: `${window.apiBaseUrl}/${ratingId}/rating/delete`,
                method: 'DELETE'
            });
        }
    };

    // ============================================
    // Auto-initialization on page load
    // ============================================

    $(document).ready(function() {
        // Detect page type from body or main element
        const pageType = $('body').data('page-type') || $('main').data('page-type');

        // Initialize based on page type
        if (pageType === 'movie' || pageType === 'series' || pageType === 'episode') {
            // Initialize watchlist button if exists
            if ($('#addToWatchlist').length) {
                WatchlistManager.initButton('#addToWatchlist');
            }

            // Initialize favorite button if exists
            if ($('#addToFavorite').length) {
                FavoritesManager.initButton('#addToFavorite');
            }
        }

        console.log('Content Interactions initialized');
    });

})(jQuery);

