<?php


// dashboard routes

use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard',function(){
    return redirect()->route('dashboard.home');
});
Route::group([
    'prefix' => 'dashboard',
    'middleware' => ['auth:admin'],
    'as' => 'dashboard.'
], function () {
    /* ********************************************************** */

    // Dashboard ************************
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Logs ************************
    Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('getLogs', [ActivityLogController::class, 'getLogs'])->name('logs.getLogs');

    // users ************************
    Route::get('profile/settings', [AdminController::class, 'settings'])->name('profile.settings');

    /* ********************************************************** */


    /* ********************************************************** */

    // filters
    Route::get('users-filters/{column}', [UserController::class, 'getFilterOptions'])->name('users.filters');
    Route::get('admins-filters/{column}', [AdminController::class, 'getFilterOptions'])->name('admins.filters');

    // Resources
    // Route::resource('constants', ConstantController::class)->only(['index', 'store', 'destroy']);

    // resources
    Route::resources([
        'users' => UserController::class,
        'admins' => AdminController::class,
        'media' => MediaController::class,
    ]);
    /* ********************************************************** */
});
