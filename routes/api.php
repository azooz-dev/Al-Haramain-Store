<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Product\ProductReviewController;

// Products
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Categories
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

// Orders
Route::apiResource('orders', OrderController::class)->only('store');

// User Orders
Route::apiResource('user/orders', UserOrderController::class)->only(['index']);
