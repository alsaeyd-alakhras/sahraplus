<?php

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

// ================================
// ⭐ User Interaction (المرحلة الثالثة)
// ================================
use App\Http\Controllers\API\V1\WatchlistsController;
use App\Http\Controllers\API\V1\ProgressController;
use App\Http\Controllers\API\V1\HistoryController;
use App\Http\Controllers\API\V1\RatingsController;
use App\Http\Controllers\API\V1\FavoritesController;
use App\Http\Controllers\API\V1\DownloadsController;
use App\Http\Controllers\API\V1\ShortController;

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
    Route::apiResource('categories', CategoryController::class)->only(['index','show']);

    // 🎬 Movies
    Route::apiResource('movies', MoviesController::class)->only(['index','show']);
    
    // 🎬 Shorts
    Route::apiResource('shorts', ShortController::class)->only(['index','show']);
    
    // Shorts interactions (like/save/share)
    Route::post('shorts/{id}/like', [ShortController::class, 'like']);
    Route::post('shorts/{id}/save', [ShortController::class, 'save']);
    Route::post('shorts/{id}/share', [ShortController::class, 'share']);
    Route::post('shorts/{id}/view', [ShortController::class, 'view']);

    // 📺 Series
    Route::apiResource('series', SeriesController::class)->only(['index','show']);
    // مواسم مسلسل محدد
    Route::get('series/{series}/seasons', [SeasonController::class, 'bySeries']);

    // 📦 Seasons
    Route::apiResource('seasons', SeasonController::class)->only(['show']);
    // حلقات موسم محدد
    Route::get('seasons/{season}/episodes', [EpisodeController::class, 'bySeason']);

    // 🎞 Episodes
    Route::apiResource('episodes', EpisodeController::class)->only(['show']);

    // 👥 People
    Route::apiResource('people', PeopleController::class)->only(['index','show']);

    // 💬 Comments (حسب النوع movie|series|episode|short)
    Route::get('{type}/{id}/comments', [CommentController::class, 'index']);

    // 🔎 Search & Filter
    Route::get('search', [SearchController::class, 'index'])->name('search');


    // ================================
    // ⭐ User Interaction (المرحلة الثالثة)
    // ================================
    Route::middleware(['auth:sanctum','throttle:api'])->group(function () {

        // 📌 Watchlists
        Route::get('watchlists', [WatchlistsController::class, 'index']);
        Route::get('{type}/{id}/watchlist/status', [WatchlistsController::class, 'status']);

        // 📌 Progress
        Route::get('progress/{type}/{id}', [ProgressController::class, 'show']);

        // 📌 History
        Route::get('history', [HistoryController::class, 'index']);

        // 📌 Ratings
        Route::get('ratings/{type}/{id}', [RatingsController::class, 'show']);

        // 📌 Favorites
        Route::get('favorites', [FavoritesController::class, 'index']);
        Route::get('{type}/{id}/favorite/status', [FavoritesController::class, 'status']);

        // 📌 Downloads
        Route::get('downloads', [DownloadsController::class, 'index']);
        Route::get('downloads/{download}', [DownloadsController::class, 'show']);
    });
});
