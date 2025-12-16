<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\Coupon\CouponController;

// Coupon routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('coupons/{code}/{userId}', [CouponController::class, 'apply']);
});
