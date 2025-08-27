<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\Dashboard\HomeController;

use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\MediaController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\UserAvatarController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\SystemSettingsController;

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

        /* ********************************************************** */

        // filters
        Route::get('users-filters/{column}', [UserController::class, 'getFilterOptions'])->name('users.filters');
        Route::get('admins-filters/{column}', [AdminController::class, 'getFilterOptions'])->name('admins.filters');
        Route::get('countries-filters/{column}', [CountryController::class, 'getFilterOptions'])->name('countries.filters');
        Route::get('movies-filters/{column}', [ MoviesController::class, 'getFilterOptions'])->name('movies.filters');
        Route::get('people-filters/{column}', [ PeopleController::class, 'getFilterOptions'])->name('people.filters');


        // resources
        Route::resources([
            'users' => UserController::class,
            'admins' => AdminController::class,
            'media' => MediaController::class,
            'user_avatars' => UserAvatarController::class,
            'countries' => CountryController::class,
            'movies'    => MoviesController::class,
            'people'    => PeopleController::class,
        ]);
    });
});
