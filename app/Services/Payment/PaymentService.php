<?php

namespace App\Services\Payment;

use App\DTOs\PaymentResult;
use Modules\Order\Entities\Order\Order;

use function App\Helpers\errorResponse;
use App\Contracts\PaymentProcessorInterface;
use App\Services\Payment\PaymentProcessorFactory;
use App\Repositories\Interface\Payment\PaymentRepositoryInterface;
use App\Exceptions\Payment\CreatePaymentException;

class PaymentService
{
  public function __construct(
    private PaymentRepositoryInterface $paymentRepository,
    private PaymentProcessorFactory $processorFactory
  ) {
    $this->paymentRepository = $paymentRepository;
  }

  private function getProcessor(string $paymentMethod): PaymentProcessorInterface
  {
    return $this->processorFactory->make($paymentMethod);
  }

  public function createPaymentIntent(array $orderData): ?string
  {
    $processor = $this->getProcessor($orderData['payment_method']);

    if (!$processor->requiresPaymentIntent()) {
      return null;
    }

    return $processor->createPaymentIntent($orderData);
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
