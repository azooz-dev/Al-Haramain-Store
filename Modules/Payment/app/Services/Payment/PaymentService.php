<?php

namespace Modules\Payment\Services\Payment;

use Modules\Payment\DTOs\PaymentResult;

use function App\Helpers\errorResponse;
use Modules\Payment\Contracts\PaymentProcessorInterface;
use Modules\Payment\Contracts\PaymentServiceInterface;
use Modules\Payment\Services\Payment\PaymentProcessorFactory;
use Modules\Payment\Repositories\Interface\Payment\PaymentRepositoryInterface;
use Modules\Payment\Exceptions\Payment\CreatePaymentException;

class PaymentService implements PaymentServiceInterface
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
