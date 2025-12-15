<?php

use App\Http\Controllers\Dashboard\ActiveDevicesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\HomeController;

use App\Http\Controllers\Dashboard\UserController;

use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\MediaController;
use App\Http\Controllers\Dashboard\ShortController;
use App\Http\Controllers\Dashboard\MoviesController;
use App\Http\Controllers\Dashboard\PeopleController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\UserAvatarController;
use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\EpisodeController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\SeasonController;
use App\Http\Controllers\Dashboard\SeriesController;
use App\Http\Controllers\Dashboard\SystemSettingsController;
use App\Http\Controllers\Dashboard\UserRatingController;
use App\Http\Controllers\Dashboard\DownloadController;
use App\Http\Controllers\Dashboard\PlanAccessController;
use App\Http\Controllers\Dashboard\SubscriptionPlanController;
use App\Http\Controllers\Dashboard\PlanLimitationController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\TaxController;
use App\Http\Controllers\Dashboard\UserSubscriptionController;
use App\Http\Controllers\Dashboard\PaymentsController;
use App\Models\Payments;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
    ],
], function () {

    // redirect to localized /{locale}/dashboard/home
    Route::get('dashboard', function () {
        return redirect()->route('dashboard.home');
    });

    Route::group([
        'prefix' => 'dashboard',
        'middleware' => ['auth:admin'],
        'as' => 'dashboard.',
    ], function () {
        /* ********************************************************** */

        // Dashboard ************************
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        // Logs ************************
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('getLogs', [ActivityLogController::class, 'getLogs'])->name('logs.getLogs');
        // admin ************************
        Route::get('profile/settings', [AdminController::class, 'settings'])->name('profile.settings');

        // notifications ************************
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::get('notifications-filters/{column}', [NotificationController::class, 'getFilterOptions'])->name('notifications.filters');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

        // Settings ************************
        Route::get('settings', [SystemSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SystemSettingsController::class, 'update'])->name('settings.update');

        // Series ************************
        Route::get('episodes/checkEpisodNumber', [EpisodeController::class, 'checkEpisodNumber'])->name('episodes.checkEpisodNumber');

        // Movies
        Route::get('movies/castRowPartial', [MoviesController::class, 'castRowPartial'])->name('movies.castRowPartial');
        Route::get('movies/castSubRowPartial', [MoviesController::class, 'subRowPartial'])->name('movies.subRowPartial');
        Route::get('movies/videoRowPartial', [MoviesController::class, 'videoRowPartial'])->name('movies.videoRowPartial');
        Route::get('movies/subtitleRowPartial', [MoviesController::class, 'subtitleRowPartial'])->name('movies.subtitleRowPartial');
        Route::get('movies/popular', [MoviesController::class, 'fetchFromTMDB']);
        Route::get('movies/tmdb-sync/{id}', [MoviesController::class, 'syncFromTmdb'])->name('movies.tmdb.sync');

        Route::get('people/search', [PeopleController::class, 'search'])->name('people.search');

        Route::get('shorts/videoRowPartial', [ShortController::class, 'videoRowPartial'])->name('shorts.videoRowPartial');
        Route::delete('video-files/{id}', [ShortController::class, 'deleteVideo'])->name('shorts.video-files.delete');
        Route::delete('movies-video-files/{id}', [MoviesController::class, 'deleteVideo'])->name('movies.video-files.delete');
        Route::delete('movies-subtitles/{id}', [MoviesController::class, 'deleteSubtitle'])->name('movies.video-files.delete');
        Route::delete('movies-casts/{id}', [MoviesController::class, 'deleteCast'])->name('movies.video-files.delete');
        Route::delete('series-casts/{id}', [SeriesController::class, 'deleteCast'])->name('movies.video-files.delete');
        Route::delete('episodes-video-files/{id}', [EpisodeController::class, 'deleteVideo'])->name('episodes.video-files.delete');
        Route::delete('episodes-subtitles/{id}', [EpisodeController::class, 'deleteSubtitle'])->name('episodes.video-files.delete');


        Route::get('series/castRowPartial', [SeriesController::class, 'castRowPartial'])
            ->name('series.castRowPartial');

        Route::get('episodes/videoRowPartial', [EpisodeController::class, 'videoRowPartial'])->name('episodes.videoRowPartial');
        Route::get('episodes/subtitleRowPartial', [EpisodeController::class, 'subtitleRowPartial'])->name('episodes.subtitleRowPartial');


        /* ********************************************************** */

        // filters
        Route::get('users-filters/{column}', [UserController::class, 'getFilterOptions'])->name('users.filters');
        Route::get('admins-filters/{column}', [AdminController::class, 'getFilterOptions'])->name('admins.filters');
        Route::get('countries-filters/{column}', [CountryController::class, 'getFilterOptions'])->name('countries.filters');
        Route::get('userRatings-filters/{column}', [UserRatingController::class, 'getFilterOptions'])->name('userRatings.filters');
        Route::get('downloads-filters/{column}', [DownloadController::class, 'getFilterOptions'])->name('downloads.filters');
        Route::get('sub_plans-filters/{column}', [SubscriptionPlanController::class, 'getFilterOptions'])->name('sub_plans.filters');
        Route::get('taxes-filters/{column}', [TaxController::class, 'getFilterOptions'])->name('taxes.filters');
        Route::delete('sub-plans-delete/{id}', [SubscriptionPlanController::class, 'deleteCast'])->name('movies.video-files.delete');
        Route::delete('country-delete/{id}', [SubscriptionPlanController::class, 'delete_country']);

        Route::get('users_subscription-filters/{column}', [UserSubscriptionController::class, 'getFilterOptions'])->name('users_subscription.filters');

        Route::get('movies-filters/{column}', [MoviesController::class, 'getFilterOptions'])->name('movies.filters');
        Route::get('people-filters/{column}', [PeopleController::class, 'getFilterOptions'])->name('people.filters');
        Route::get('short-filters/{column}', [ShortController::class, 'getFilterOptions'])->name('short.filters');
        Route::get('coupons-filters/{column}', [CouponController::class, 'getFilterOptions'])->name('coupons.filters');
        Route::get('movie-categories-filters/{column}', [CategoryController::class, 'getFilterOptions'])->name('movie-categories.filters');
        Route::get('series-filters/{column}', [SeriesController::class, 'getFilterOptions'])->name('series.filters');
        Route::get('plan_access-filters/{column}', [PlanAccessController::class, 'getFilterOptions'])->name('plan_access.filters');
        Route::get('plan_access/get-contents', [PlanAccessController::class, 'getContents'])->name('plan_access.getContents');

        Route::resource('seasons', SeasonController::class)->except(['index']);
        Route::resource('episodes', EpisodeController::class)->except(['index']);
        Route::resource('shorts', ShortController::class)->parameters(['shorts' => 'short'])->names('shorts');

        Route::get('payments-filters/{column}', [PaymentsController::class, 'getFilterOptions'])->name('payments.filters');

        // resources
        Route::resources([
            'users' => UserController::class,
            'admins' => AdminController::class,
            'media' => MediaController::class,
            'user_avatars' => UserAvatarController::class,
            'countries' => CountryController::class,
            'userRatings' => UserRatingController::class,
            'downloads' => DownloadController::class,
            'sub_plans' => SubscriptionPlanController::class,
            'plan_access' => PlanAccessController::class,
            'coupons' => CouponController::class,
            'taxes' => TaxController::class,
            'users_subscription' => UserSubscriptionController::class,
            'active_devices' => ActiveDevicesController::class,
            'movies'    => MoviesController::class,
            'payments'    => PaymentsController::class,
            'people'    => PeopleController::class,
            // 'shorts'    => ShortController::class,
            'movie-categories'    => CategoryController::class,
            'series'    => SeriesController::class,
        ]);

        Route::get('series/tmdb-sync/{id}', [SeriesController::class, 'syncSeriesFromTmdb'])->name('series.tmdb.sync');
        //Route::get('episodes/tmdb-sync/{id}', [EpisodeController::class, 'syncEpisodeFromTmdb'])->name('series.tmdb.sync');
        Route::get('episodes/tmdb-sync/{tmdbId}/{seasonNumber}/{episodeNumber}', [EpisodeController::class, 'syncEpisodeFromTmdb']);


        Route::get('countryPrice/countryRowPartial', [SubscriptionPlanController::class, 'countryRowPartial'])->name('countryPrice.countryRowPartial');
        Route::get('limitations/limitationsRowPartial', [SubscriptionPlanController::class, 'limitationsRowPartial'])->name('limitations.limitationsRowPartial');
    });

    Route::get('/countries/{id}/currency', function ($id) {
        $country = \App\Models\Country::find($id);

        return response()->json([
            'currency' => $country?->currency ?? ''
        ]);
    });
});
