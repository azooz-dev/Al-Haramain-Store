<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\Product\ProductController;
use Modules\Catalog\Http\Controllers\Category\CategoryController;

// Products
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Categories
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
