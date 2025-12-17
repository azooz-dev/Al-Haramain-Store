<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ResendEmailVerificationController;


Route::middleware([StartSession::class, 'set.locale'])->group(function () {
  Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user', [AuthController::class, 'user']);
  });

  // Auth Routes
  Route::post('login', [AuthController::class, 'login']);
  Route::post('register', [AuthController::class, 'register']);
  Route::post('users/email/verify-code', [EmailVerificationController::class, 'verify']);
  Route::post('users/email/resend-code', [ResendEmailVerificationController::class, 'resend'])->middleware('throttle:3,1');
  Route::post("/forget-password", [ForgetPasswordController::class, 'forget']);
  Route::post("/reset-password", [ResetPasswordController::class, 'reset']);
});
