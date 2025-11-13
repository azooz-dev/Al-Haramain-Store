<?php

namespace App\Services\Payment;

use App\DTOs\PaymentResult;
use App\Models\Order\Order;

use function App\Helpers\errorResponse;
use function App\Helpers\showOne;
use App\Contracts\PaymentProcessorInterface;
use App\Exceptions\Payment\InvalidPaymentMethodException;
use App\Services\Payment\Processors\StripePaymentProcessor;

use App\Services\Payment\Processors\CashOnDeliveryProcessor;
use App\Repositories\Interface\Payment\PaymentRepositoryInterface;
use App\Exceptions\Payment\CreatePaymentException;

class PaymentService
{
  public function __construct(private PaymentRepositoryInterface $paymentRepository)
  {
    $this->paymentRepository = $paymentRepository;
  }

  private function getProcessor(string $paymentMethod): PaymentProcessorInterface
  {
    return match ($paymentMethod) {
      Order::PAYMENT_METHOD_CASH_ON_DELIVERY => new CashOnDeliveryProcessor(),
      Order::PAYMENT_METHOD_CREDIT_CARD => new StripePaymentProcessor(),
      default => throw new InvalidPaymentMethodException(__('app.messages.payment.invalid_payment_method'), 400),
    };
  }

  public function createPaymentIntent(array $orderData): ?string
  {
    $processor = $this->getProcessor($orderData['payment_method']);

    if (!$processor->requiresPaymentIntent()) {
      return null;
    }

    $paymentIntent = $processor->createPaymentIntent($orderData);

    return showOne($paymentIntent);
  }

  public function processPayment(array $orderData): ?PaymentResult
  {
    $processor = $this->getProcessor($orderData['payment_method']);
    return $processor->processPayment($orderData);
  }

  public function createPayment(int $orderId, PaymentResult $paymentResult)
  {
    try {
      $payment = $this->paymentRepository->create($orderId, $paymentResult);

      return $payment;
    } catch (CreatePaymentException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }
}
