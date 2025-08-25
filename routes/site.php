<?php

use App\Http\Controllers\Site\FrontController;
use App\Http\Controllers\Site\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => '',
    'as' => 'site.',
    // 'middleware' => ['auth'],
], function () {
    Route::get('/',[FrontController::class,'index'])->name('home');
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
});
