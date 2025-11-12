<?php

namespace App\Contracts;

interface PaymentProcessorInterface
{
  public function requiresPaymentIntent(): bool;

  public function createPaymentIntent(array $orderData): ?string;

  public function processPayment(array $orderData);

  public function verifyPayment(string $paymentIntentId);
}
