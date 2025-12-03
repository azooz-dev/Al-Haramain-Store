<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Offer\OfferController;
use App\Http\Controllers\Order\OrderController;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Controllers\Coupon\CouponController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\User\UserFavoriteController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\User\Order\UserOrderController;
use App\Http\Controllers\Payment\StripeWebhookController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ResendEmailVerificationController;
use App\Http\Controllers\User\UserAddresses\UserAddressController;
use App\Http\Controllers\User\Product\UserProductFavoriteController;
use App\Http\Controllers\User\Order\OrderItem\Review\UserOrderItemReviewController;


// Stripe Webhook
Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);

Route::middleware([StartSession::class, 'set.locale'])->group(function () {
  Route::middleware('auth:sanctum')->group(function () {
    // Orders
    Route::apiResource('orders', OrderController::class)->only(['store', 'show']);

    // Payments
    Route::post('payments/create-intent', [PaymentController::class, 'createPaymentIntent']);

    // User Orders
    Route::apiResource('users.orders.items.reviews', UserOrderItemReviewController::class)->only('store');

    // Coupons
    Route::get('coupons/{code}/{userId}', [CouponController::class, 'apply']);

    // User Products Favorite
    Route::apiResource('users.products.colors.variants.favorites', UserProductFavoriteController::class)->only('store');

    // User Favorites
    Route::apiResource('users.favorites', UserFavoriteController::class)->only(['index', 'destroy']);

    Route::post('logout', [AuthController::class, 'logout']);

    // Users
    Route::apiResource('users', UserController::class)->only(['update', 'destroy']);

    // User Orders
    Route::apiResource('users.orders', UserOrderController::class)->only('index');

    // User Addresses
    Route::apiResource('users.addresses', UserAddressController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('user', [AuthController::class, 'user']);
  });

  // Offers
  Route::apiResource('offers', OfferController::class)->only(['index', 'show']);

  // Auth Routes
  Route::post('login', [AuthController::class, 'login']);
  Route::post('register', [AuthController::class, 'register']);
  Route::post('users/email/verify-code', [EmailVerificationController::class, 'verify']);
  Route::post('users/email/resend-code', [ResendEmailVerificationController::class, 'resend'])->middleware('throttle:3,1');
  Route::post("/forget-password", [ForgetPasswordController::class, 'forget']);
  Route::post("/reset-password", [ResetPasswordController::class, 'reset']);
});
