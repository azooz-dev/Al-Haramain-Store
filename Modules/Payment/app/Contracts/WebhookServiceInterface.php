<?php

namespace Modules\Payment\Contracts;

use Stripe\PaymentIntent;

interface WebhookServiceInterface
{
    /**
     * Handle successful payment webhook event
     */
    public function handlePaymentSucceeded(PaymentIntent $paymentIntent): void;

    /**
     * Handle failed payment webhook event
     */
    public function handlePaymentFailed(PaymentIntent $paymentIntent): void;

    /**
     * Handle canceled payment webhook event
     */
    public function handlePaymentCanceled(PaymentIntent $paymentIntent): void;
}
