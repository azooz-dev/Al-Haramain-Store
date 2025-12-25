<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    // Ensure we only return user if authenticated via bearer token
    if (!$request->bearerToken()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    // Return user wrapped in resource format
    return \App\Helpers\showOne(new \Modules\User\app\Http\Resources\UserApiResource($user), 'User', 200);
});

Route::group(['middleware' => ['api', 'set.locale']], function () {
    // Reviews
});
