<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\Http\Controllers\ReviewController;
use Illuminate\Session\Middleware\StartSession;

// Public review routes
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

// Protected review routes (require authentication)
Route::middleware([StartSession::class, 'set.locale'])->group(function () {
  Route::middleware('auth:sanctum')->group(function () {
    Route::put('reviews/{review}', [ReviewController::class, 'update']);
    Route::patch('reviews/{review}', [ReviewController::class, 'update']);
  });
});
