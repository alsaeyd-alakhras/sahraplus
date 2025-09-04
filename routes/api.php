<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\UserAvatarController;
use App\Http\Controllers\API\V1\CountryController;
use App\Http\Controllers\API\V1\MoviesController;
use App\Http\Controllers\API\V1\PeopleController;
use App\Http\Controllers\API\V1\ShortController;
use App\Http\Controllers\API\V1\SeriesController;
use App\Http\Controllers\API\V1\SystemSettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| هذه الواجهة البرمجية مخصصة لتطبيق الهاتف / الفرونت فقط.
| لا تشمل إدارة المشرف أو لوحة التحكم.
*/

//
// 🟡 Public Routes (بدون تسجيل دخول)
//
Route::prefix('v1')->name('api.v1.')->group(function () {

    // 🔐 Auth - تسجيل دخول وتسجيل حساب جديد
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // 📄 Optional: استرجاع إعدادات عامة بدون تسجيل دخول مثل قائمة الدول
    // Route::get('countries', [CountryController::class, 'index']);
    // 📌 User Avatars
    Route::apiResource('user_avatars', UserAvatarController::class)->only(['index', 'show']);

    // 📌 Countries
    Route::apiResource('countries', CountryController::class)->only(['index', 'show']);

    // 🎬 Movies
    Route::apiResource('movies', MoviesController::class)->only(['index', 'show']);

    // 👥 People
    Route::apiResource('people', PeopleController::class)->only(['index', 'show']);

    // 🎞 Shorts
    Route::apiResource('shorts', ShortController::class)->only(['index', 'show']);

    // 📺 Series
    Route::apiResource('series', SeriesController::class)->only(['index', 'show']);

    // ⚙️ System Settings (عرض الإعدادات العامة)
    Route::get('settings', [SystemSettingsController::class, 'edit'])->name('settings.edit');
});

//
// 🟢 Protected Routes (تتطلب تسجيل دخول)
//
Route::middleware(['auth:sanctum','throttle:api'])->prefix('v1')->name('api.v1.')->group(function () {

    // 👤 User - الملف الشخصي
    Route::get('me', [UserController::class, 'me']); // استرجاع معلومات المستخدم الحالي
    Route::put('me', [UserController::class, 'update']); // تحديث معلومات المستخدم
    Route::put('me/password', [UserController::class, 'changePassword']); // تغيير كلمة المرور
    Route::get('me/profiles', [UserController::class, 'profiles']); // الملفات الشخصية
    Route::get('me/sessions', [UserController::class, 'sessions']); // الجلسات النشطة

    // 🧾 Notifications - الإشعارات
    Route::get('me/notifications', [UserController::class, 'notifications']);
    Route::post('me/notifications/{id}/read', [UserController::class, 'markNotificationRead']);

    // ⛔️ حذف الحساب (اختياري)
    // Route::delete('me', [UserController::class, 'destroy']);
});
