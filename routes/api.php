<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Offer\OfferController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Coupon\CouponController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\User\Order\Product\Review\UserOrderProductReviewController;


// Products
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Categories
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

// Orders
Route::apiResource('orders', OrderController::class)->only('store');

// User Orders
Route::apiResource('users.orders.products.reviews', UserOrderProductReviewController::class)->only('store');

// Offers
Route::apiResource('offers', OfferController::class)->only(['index', 'show']);

// Coupons
Route::get('coupons/{id}', [CouponController::class, 'apply']);

// Auth Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);
