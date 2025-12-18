<?php

namespace Modules\Payment\Contracts;

use Modules\Payment\DTOs\PaymentResult;

interface PaymentServiceInterface
{
    /**
     * Create a payment intent for the given order data.
     *
     * @param array $orderData
     * @return string|null Payment intent client secret, or null if not required
     */
    public function createPaymentIntent(array $orderData): ?string;

    /**
     * Process payment for the given order data.
     *
     * @param array $orderData
     * @return PaymentResult|null
     */
    public function processPayment(array $orderData): ?PaymentResult;

    /**
     * Create a payment record for the given order.
     *
     * @param int $orderId
     * @param PaymentResult $paymentResult
     * @return mixed
     */
    public function createPayment(int $orderId, PaymentResult $paymentResult);
}

