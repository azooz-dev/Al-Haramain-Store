<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Http\Controllers\UserFavoriteController;
use Modules\User\Http\Controllers\UserOrderController;
use Modules\User\Http\Controllers\UserAddressController;
use Modules\User\Http\Controllers\UserProductFavoriteController;
use Modules\User\Http\Controllers\UserOrderItemReviewController;

Route::middleware('auth:sanctum')->group(function () {
    // User Orders
    Route::apiResource('users.orders.items.reviews', UserOrderItemReviewController::class)->only('store');

    // User Products Favorite
    Route::apiResource('users.products.colors.variants.favorites', UserProductFavoriteController::class)->only('store');

    // User Favorites
    Route::apiResource('users.favorites', UserFavoriteController::class)->only(['index', 'destroy']);

    // Users
    Route::apiResource('users', UserController::class)->only(['update', 'destroy']);

    // User Orders
    Route::apiResource('users.orders', UserOrderController::class)->only('index');

    // User Addresses
    Route::apiResource('users.addresses', UserAddressController::class)->only(['index', 'store', 'update', 'destroy']);
});
