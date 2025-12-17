<?php

use Illuminate\Support\Facades\Route;
use Modules\Offer\Http\Controllers\OfferController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('offers', OfferController::class)->names('offer');
});
