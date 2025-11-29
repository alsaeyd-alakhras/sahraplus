<?php

use App\Http\Controllers\Frontend\MovieController;
use App\Http\Controllers\Frontend\FrontController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\SeriesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/login', function () {
    return redirect()->route('login');
});

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
    ],
], function () {

    Route::group([
        'prefix' => '',
        'as' => 'site.',
        // 'middleware' => ['auth'],
    ], function () {
        Route::get('/', [FrontController::class, 'index'])->name('home');
        Route::get('/categories', [FrontController::class, 'categories'])->name('categories');
        Route::get('/categories/{category}', [FrontController::class, 'categoryShow'])->name('categories.show');
        Route::get('/actors', [FrontController::class, 'actors'])->name('actors');
        Route::get('/shorts', [FrontController::class, 'shorts'])->name('shorts');

        Route::get('/cast/{id}', [FrontController::class, 'cast'])->name('cast');

        // Movie routes
        Route::get('/movies', [MovieController::class, 'index'])->name('movies');
        Route::prefix('movies')->name('movie.')->group(function () {

            // عرض الفيلم
            Route::get('/html_sections', [MovieController::class, 'getHtmlSection'])->name('get-html-section');
            Route::get('/sections', [MovieController::class, 'getSections'])->name('sections');

            Route::get('/{slug}', [MovieController::class, 'show'])->name('show');
        });
        // API routes for AJAX calls
        Route::prefix('api')->name('api.')->group(function () {
            Route::middleware('auth')->group(function () {
                // Comments
                Route::post('/movies/{id}/comments', [MovieController::class, 'addComment'])->name('movie.comment');
                // Watchlist
                Route::post('/watchlist/{id}', [MovieController::class, 'toggleWatchlist'])->name('watchlist.toggle');
                // View count
                Route::post('/movies/{id}/view', [MovieController::class, 'incrementView'])->name('movie.view');
                Route::post('/movies/{id}/progress', [MovieController::class, 'updateWatchProgress'])->name('movie.progress');
            });
        });

        // Series routes
        Route::get('/series', [SeriesController::class, 'index'])->name('series');
        Route::prefix('series')->name('series.')->group(function () {

            // عرض الفيلم
            Route::get('/html_sections', [SeriesController::class, 'getHtmlSection'])->name('get-html-section');
            Route::get('/sections', [SeriesController::class, 'getSections'])->name('sections');

            Route::get('/{slug}', [SeriesController::class, 'show'])->name('show');
            // site.series.season.show
            Route::get('/season/{id}', [SeriesController::class, 'seasonShow'])->name('season.show');
            Route::get('/episode/{id}', [SeriesController::class, 'episodeShow'])->name('episode.show');
        });
    });

    Route::group([
        'prefix' => '',
        'as' => 'site.',
        'middleware' => ['auth:web'],
    ], function () {
        // Live TV ************************
        Route::get('/live-tv', [FrontController::class, 'liveTv'])->name('live-tv');

        // User Lists (Phase 3) ************************
        Route::get('/watchlist', function () {
            return view('site.watchlist');
        })->name('watchlist');

        Route::get('/favorites', function () {
            return view('site.favorites');
        })->name('favorites');

        Route::get('/history', function () {
            return view('site.history');
        })->name('history');

        // profiles ************************
        Route::get('/profiles', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profiles', [ProfileController::class, 'store'])->name('profile.store');
        Route::put('/profiles/{profile}', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profiles/{profile}', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/profiles/{profile}/verify-pin', [ProfileController::class, 'verifyPin'])->name('profile.verify-pin');
        Route::post('/profiles/{profile}/reset-pin', [ProfileController::class, 'resetPin'])->name('profile.reset-pin');

        // settings ************************
        Route::get('settings', [FrontController::class, 'settings'])->name('settings');
        Route::post('settings/change-password', [FrontController::class, 'changePassword'])->name('change-password');
        Route::post('settings/update-personal-info', [FrontController::class, 'updatePersonalInfo'])->name('update-personal-info');
    });
});
