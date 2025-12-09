<?php

use App\Http\Controllers\API\V1\BillingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return redirect()->route('dashboard.home');
// })->name('home');

Route::get('pay/test/{subscription_id}', [BillingController::class, 'pay']);
Route::match(['get', 'post'], '/payment/callback', [BillingController::class, 'callback'])->name('payment.callback');
Route::match(['get', 'post'], '/payment/cancel', [BillingController::class, 'cancel'])->name('payment.cancel');

require __DIR__ . '/site.php';
require __DIR__ . '/dashboard.php';
