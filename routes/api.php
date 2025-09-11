<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Offer\OfferController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Coupon\CouponController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ResendEmailVerificationController;
use App\Http\Controllers\User\Order\Product\Review\UserOrderProductReviewController;
use Illuminate\Session\Middleware\StartSession;

Route::middleware([StartSession::class, 'set.locale'])->group(function () {
  // Products
  Route::apiResource('products', ProductController::class)->only(['index', 'show']);

  // Categories
  Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

  Route::middleware('auth:sanctum')->group(function () {
    // Orders
    Route::apiResource('orders', OrderController::class)->only('store');

    // User Orders
    Route::apiResource('users.orders.products.reviews', UserOrderProductReviewController::class)->only('store');

    // Coupons
    Route::get('coupons/{id}', [CouponController::class, 'apply']);

    Route::post('logout', [AuthController::class, 'logout']);
  });

  // Offers
  Route::apiResource('offers', OfferController::class)->only(['index', 'show']);

  // Auth Routes
  Route::post('login', [AuthController::class, 'login']);
  Route::post('register', [AuthController::class, 'register']);
  Route::get('user', [AuthController::class, 'user']);
  Route::post('users/email/verify-code', [EmailVerificationController::class, 'verify']);
  Route::post('users/email/resend-code', [ResendEmailVerificationController::class, 'resend'])->middleware('throttle:3,1');
  Route::post("/forget-password", [ForgetPasswordController::class, 'forget']);
  Route::post("/reset-password", [ResetPasswordController::class, 'reset']);
});
