<?php

use Illuminate\Support\Facades\Route;
use Modules\Offer\Http\Controllers\Offer\OfferController;

// Offer routes (public)
Route::middleware([\Illuminate\Session\Middleware\StartSession::class, 'set.locale'])->group(function () {
    Route::apiResource('offers', OfferController::class)->only(['index', 'show']);
});
