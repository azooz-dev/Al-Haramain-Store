<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\CouponController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('coupons', CouponController::class)->names('coupon');
});
