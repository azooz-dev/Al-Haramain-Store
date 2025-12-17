<?php

use Illuminate\Support\Facades\Route;
use Modules\Favorite\Http\Controllers\FavoriteController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('favorites', FavoriteController::class)->names('favorite');
});
