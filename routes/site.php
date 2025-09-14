<?php

use App\Http\Controllers\Frontend\MovieController;
use App\Http\Controllers\Frontend\FrontController;
use App\Http\Controllers\Frontend\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
        Route::get('/',[FrontController::class,'index'])->name('home');
        Route::get('/series',[FrontController::class,'series'])->name('series');
        Route::get('/movies',[FrontController::class,'movies'])->name('movies');
        Route::get('/live',[FrontController::class,'live'])->name('live');
        Route::get('/categories',[FrontController::class,'categories'])->name('categories');
        Route::get('/actors',[FrontController::class,'actors'])->name('actors');


        // Movie routes
        Route::prefix('movies')->name('movie.')->group(function () {
            // عرض الفيلم
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
    });

    Route::group([
        'prefix' => '',
        'as' => 'site.',
        'middleware' => ['auth:web'],
    ], function () {
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
