<?php

use App\Http\Controllers\API\V1\AnalyticsController;
use App\Http\Controllers\Frontend\ProfileController;
use Illuminate\Support\Facades\Route;

// ================================
// 🔐 Auth & User
// ================================
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\UserAvatarController;
use App\Http\Controllers\API\V1\CountryController;
use App\Http\Controllers\API\V1\SystemSettingsController;

// ================================
// 🎬 Content (المرحلة الثانية)
// ================================
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\MoviesController;
use App\Http\Controllers\API\V1\SeriesController;
use App\Http\Controllers\API\V1\SeasonController;
use App\Http\Controllers\API\V1\EpisodeController;
use App\Http\Controllers\API\V1\PeopleController;
use App\Http\Controllers\API\V1\CommentController;
use App\Http\Controllers\API\V1\SearchController;
use App\Http\Controllers\API\V1\SubscriptionPlansController;
use App\Http\Controllers\API\V1\UserSubscriptionController;

// ================================
// ⭐ User Interaction (المرحلة الثالثة)
// ================================
use App\Http\Controllers\API\V1\WatchlistsController;
use App\Http\Controllers\API\V1\WatchProgresController;
use App\Http\Controllers\API\V1\ViewingHistoryController;
use App\Http\Controllers\API\V1\UserRatingController;
use App\Http\Controllers\API\V1\FavoritesController;
use App\Http\Controllers\API\V1\DownloadsController;
use App\Http\Controllers\API\V1\ShortController;
use App\Http\Controllers\API\V1\EPGController;
use App\Http\Controllers\API\V1\LiveTvController;
use App\Http\Controllers\API\V1\BillingController;
use App\Http\Controllers\API\V1\CouponController;
use App\Http\Controllers\API\V1\MangeDevicesController;
use App\Http\Controllers\API\V1\PaymentController;
use App\Http\Controllers\API\V1\HomeBannerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| جميع المسارات هنا خاصة بتطبيق الهاتف / الفرونت فقط (Read-only).
| التعديلات CRUD تتم عبر لوحة التحكم (Dashboard).
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    // ================================
    // 🔐 Auth & User
    // ================================
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // 🧑 User Profile & Notifications
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('me', [UserController::class, 'me']);
        Route::put('me', [UserController::class, 'update']);
        Route::put('me/password', [UserController::class, 'changePassword']);
        Route::get('me/profiles', [UserController::class, 'profiles']);
        Route::get('me/sessions', [UserController::class, 'sessions']);

        // 🔔 Notifications
        Route::get('me/notifications', [UserController::class, 'notifications']);
        Route::post('me/notifications/{id}/read', [UserController::class, 'markNotificationRead']);

        // profiles ************************
        Route::get('/profiles', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profiles', [ProfileController::class, 'store'])->name('profile.store');
        Route::put('/profiles/{profile}', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profiles/{profile}', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/profiles/{user}/verify-pin', [ProfileController::class, 'verifyPin'])->name('profile.verify-pin');
        Route::post('/profiles/{user}/reset-pin', [ProfileController::class, 'resetPin'])->name('profile.reset-pin');
        Route::post('/profiles/{user}/create-pin', [ProfileController::class, 'createPin'])->name('profile.create-pin');
    });

    // ⚙️ Settings
    Route::get('settings', [SystemSettingsController::class, 'edit'])->name('settings.edit');

    // 📌 User Avatars
    Route::apiResource('user_avatars', UserAvatarController::class)->only(['index', 'show']);

    // 🌍 Countries
    Route::apiResource('countries', CountryController::class)->only(['index', 'show']);


    // ================================
    // 🎬 Content (المرحلة الثانية)
    // ================================


    // 📚 Categories
    // Route::get('categories', [CategoryController::class, 'index']); // قائمة كاملة
    // Route::get('categories/{id}', [CategoryController::class, 'show'])
    //     ->middleware(['auth:sanctum', 'checkPlanAccess:category']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // 🎬 Movies
    // Route::get('movies', [MoviesController::class, 'index']); // قائمة كاملة
    // Route::get('movies/{id}', [MoviesController::class, 'show'])
    //     ->middleware(['auth:sanctum', 'checkPlanAccess:movie']);
    Route::apiResource('movies', MoviesController::class)->only(['index', 'show']);

    // 📺 Series
    // Route::get('series', [SeriesController::class, 'index']); // قائمة كاملة
    // Route::get('series/{id}', [SeriesController::class, 'show'])
    //     ->middleware(['auth:sanctum', 'checkPlanAccess:series']);
    Route::apiResource('series', SeriesController::class)->only(['index', 'show']);


    // 🎬 Shorts
    Route::apiResource('shorts', ShortController::class)->only(['index', 'show']);

    // Shorts interactions (like/save/share)
    Route::post('shorts/{id}/like', [ShortController::class, 'like']);
    Route::post('shorts/{id}/save', [ShortController::class, 'save']);
    Route::post('shorts/{id}/share', [ShortController::class, 'share']);
    Route::post('shorts/{id}/view', [ShortController::class, 'view']);


    // مواسم مسلسل محدد
    Route::get('series/{series}/seasons', [SeasonController::class, 'bySeries']);

    // 📦 Seasons
    Route::apiResource('seasons', SeasonController::class)->only(['show']);
    // حلقات موسم محدد
    Route::get('seasons/{season}/episodes', [EpisodeController::class, 'bySeason']);

    // 🎞 Episodes
    Route::apiResource('episodes', EpisodeController::class)->only(['show']);

    // 👥 People
    Route::apiResource('people', PeopleController::class)->only(['index', 'show']);

    // 💬 Comments (حسب النوع movie|series|episode|short)
    Route::get('{type}/{id}/comments', [CommentController::class, 'index']);

    // 🔎 Search & Filter
    Route::get('search', [SearchController::class, 'index'])->name('search');

    // 🏠 Home Banners
    Route::get('home/banners', [HomeBannerController::class, 'index'])->name('home.banners');


    // ================================
    // ⭐ User Interaction (المرحلة الثالثة)
    // ================================
    Route::middleware(['auth:sanctum'])->group(function () {

        // 📌 Watchlists
        Route::get('watchlists', [WatchlistsController::class, 'index']);
        Route::get('{type}/{id}/watchlist/status', [WatchlistsController::class, 'status']);
        Route::post('watchlist/store', [WatchlistsController::class, 'store']);
        Route::delete('{id}/watchlist/delete', [WatchlistsController::class, 'destroy']);

        // 📌 Progress
        Route::get('progress/{type}/{id}', [WatchProgresController::class, 'show']);
        Route::put('watch-progress-update/{type}/{id}', [WatchProgresController::class, 'updateProgress']);
        Route::get('watch-progress-profiles/{profileId}/continue-watching', [WatchProgresController::class, 'continueWatching']);

        // 📌 History
        Route::get('history', [ViewingHistoryController::class, 'index']);
        Route::get('profiles/{id}/history/stats', [ViewingHistoryController::class, 'analytic_history']);

        // 📌 Ratings
        Route::get('ratings/{type}/{id}', [UserRatingController::class, 'show']);
        Route::post('rating-store/{type}/{id}', [UserRatingController::class, 'store_rating'])->middleware('throttle:ratings');
        Route::delete('{id}/rating/delete', [UserRatingController::class, 'destroy']);


        // 📌 Favorites
        Route::get('favorites', [FavoritesController::class, 'index']);
        Route::get('{type}/{id}/favorite/status', [FavoritesController::class, 'status']);
        Route::post('favorite/toggle/{type}/{id}', [FavoritesController::class, 'toggle']);

        // 📌 Downloads
        Route::get('downloads', [DownloadsController::class, 'index']);
        Route::get('completed-downloads', [DownloadsController::class, 'getCompletedDownloads']);
        Route::get('download/{download}', [DownloadsController::class, 'show']);

        Route::post('download-store/{type}/{id}', [DownloadsController::class, 'store'])->middleware('throttle:downloads');
        Route::put('Re-downloads/{id}', [DownloadsController::class, 'ReDownload']);

        //analytics
        Route::get('admin/analytics', [AnalyticsController::class, 'index']);
    });

    // ================================
    // المرحلة الرابعه
    // ================================

    //subscription_plans
    Route::get('subscription_plans', [SubscriptionPlansController::class, 'index']);
    Route::get('subscription_plan/{id}', [SubscriptionPlansController::class, 'show']);

    //user_subscription

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::get('subscriptions/check-quality', [UserSubscriptionController::class, 'checkQuality']);
        Route::get('user_subscription/me', [UserSubscriptionController::class, 'index']);

        Route::post('user_subscription', [UserSubscriptionController::class, 'store']);

        Route::post('user_subscription/confirm', [UserSubscriptionController::class, 'confirmPayment']);

        Route::post('user_subscription/cancel/{user_sub_id}', [UserSubscriptionController::class, 'cancel_user_subscription']);


        //Route::post('billing/checkout', [BillingController::class, 'checkout'])->name('api.billing.checkout');

        Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
        Route::post('billing/checkout', [PaymentController::class, 'initiateCheckout']);
        Route::post('billing/webhook', [PaymentController::class, 'handleTelrWebhook']);

        Route::post('register-device', [MangeDevicesController::class, 'registerDevice']);
        Route::post('heartbeat-device', [MangeDevicesController::class, 'heartbeat']);
    });



    // Route::post('/billing/success/{subscription_id}', [BillingController::class, 'successPayment'])->name('payment.success');

    Route::post('/billing/cancel', [UserSubscriptionController::class, 'cancel_payment'])->name('subscription.cancel');


    // 🔐 Authenticated subscription routes
    // Route::middleware(['auth:sanctum'])->group(function () {

    //     // 💳 Subscriptions
    //     Route::post('subscriptions', [SubscriptionController::class, 'store']);
    //     Route::get('subscriptions/me', [SubscriptionController::class, 'me']);
    //     Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel']);
    //     Route::get('subscriptions/check-quality', [SubscriptionController::class, 'checkQuality']);

    //     // 💰 Billing
    //     Route::post('billing/checkout', [BillingController::class, 'checkout']);

    //     // 🎟️ Coupons
    //     Route::post('coupons/validate', [CouponController::class, 'validate']);

    //     // 📱 Devices
    //     Route::post('devices/register', [DeviceController::class, 'registerDevice']);
    //     Route::post('devices/heartbeat', [DeviceController::class, 'heartbeat']);
    //     Route::get('devices', [DeviceController::class, 'index']);
    //     Route::post('devices/{deviceId}/deactivate', [DeviceController::class, 'deactivate']);
    // });

    // // 🔔 Webhooks (No authentication)


    // ================================
    // 📺 Live TV (المرحلة الخامسة)
    // ================================
    Route::prefix('live-tv')->group(function () {
        // فئات التلفاز المباشر
        Route::get('categories', [LiveTvController::class, 'categories']);

        // القنوات
        Route::get('channels', [LiveTvController::class, 'channels']);
        Route::get('categories/{id}/channels', [LiveTvController::class, 'channelsByCategory']);
        Route::get('channels/{slug}', [LiveTvController::class, 'showChannel']);

        // جدول البرامج (EPG)
        Route::get('channels/{id}/programs', [EPGController::class, 'programs']);
        Route::get('channels/{id}/programs/current', [EPGController::class, 'currentProgram']);
        Route::get('channels/{id}/programs/upcoming', [EPGController::class, 'upcomingPrograms']);

        // مشاهدة وبيانات البث
        Route::post('channels/{id}/watch', [LiveTvController::class, 'watch']);
        Route::get('channels/{id}/stream', [LiveTvController::class, 'stream']);
    });
});
