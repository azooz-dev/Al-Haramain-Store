<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Order\OrderController;
use Illuminate\Session\Middleware\StartSession;

Route::middleware([StartSession::class, 'set.locale'])->group(function () {
  Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
  });
});
