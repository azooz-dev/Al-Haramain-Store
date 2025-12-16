<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\Payment\PaymentController;
use Modules\Payment\Http\Controllers\Payment\StripeWebhookController;

// Stripe Webhook (no auth middleware)
Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);

// Payment routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('payments/create-intent', [PaymentController::class, 'createPaymentIntent']);
});
