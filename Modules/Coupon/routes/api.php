<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\Coupon\CouponController;
use Illuminate\Session\Middleware\StartSession;

// Coupon routes (require authentication)
Route::middleware([StartSession::class, 'set.locale'])->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('coupons/{code}/{userId}', [CouponController::class, 'apply']);
    });
});
