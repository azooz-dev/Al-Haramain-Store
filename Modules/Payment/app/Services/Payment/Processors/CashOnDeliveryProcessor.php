<?php

namespace Modules\Payment\Services\Payment\Processors;

use Modules\Payment\DTOs\PaymentResult;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Contracts\PaymentProcessorInterface;
use Modules\Payment\Exceptions\Payment\VerifyPaymentException;

class CashOnDeliveryProcessor implements PaymentProcessorInterface
{
  public function requiresPaymentIntent(): bool
  {
    return false;
  }

  public function createPaymentIntent(array $orderData): ?string
  {
    return null;
  }

  public function processPayment(array $orderData): PaymentResult
  {
    return new PaymentResult(
      success: true,
      transactionId: null,
      paymentMethod: Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
      amount: $orderData['total_amount'],
      paidAt: now(),
    );
  }

  public function verifyPayment(string $paymentIntentId): PaymentResult
  {
    throw new VerifyPaymentException(__('app.messages.payment.cash_on_delivery_not_supported'), 500);
  }
}
