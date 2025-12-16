<?php

namespace Modules\Payment\Contracts;

use Modules\Payment\DTOs\PaymentResult;

interface PaymentProcessorInterface
{
  public function requiresPaymentIntent(): bool;

  public function createPaymentIntent(array $orderData): ?string;

  public function processPayment(array $orderData): PaymentResult;

  public function verifyPayment(string $paymentIntentId): PaymentResult;
}
