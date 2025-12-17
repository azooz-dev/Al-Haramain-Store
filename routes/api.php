<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\Category\CategoryController;
use Modules\Catalog\Http\Controllers\Product\ProductController;
use Modules\Review\Http\Controllers\Review\ReviewController;
use Modules\Order\Http\Controllers\Order\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'set.locale'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['api', 'set.locale']], function () {
    // Categories
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // Products
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

    // Orders
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
});
