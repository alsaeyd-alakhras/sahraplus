<?php

use App\Http\Controllers\Site\FrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => '',
    // 'middleware' => ['auth'],
], function () {
    Route::get('/',[FrontController::class,'index'])->name('home');
});

Route::group([
    'prefix' => '',
    'middleware' => ['auth:web'],
], function () {

});
