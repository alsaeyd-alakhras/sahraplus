<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;

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
