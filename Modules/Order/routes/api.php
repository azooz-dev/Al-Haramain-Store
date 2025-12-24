<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Order\OrderController;

Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
