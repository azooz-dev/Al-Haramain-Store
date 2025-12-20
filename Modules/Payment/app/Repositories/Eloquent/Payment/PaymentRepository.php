<?php

namespace Modules\Payment\Repositories\Eloquent\Payment;

use Modules\Payment\DTOs\PaymentResult;
use Modules\Payment\Entities\Payment\Payment;
use Modules\Payment\Enums\PaymentStatus;
use Modules\Payment\Repositories\Interface\Payment\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
  public function create(int $orderId, PaymentResult $paymentResult): Payment
  {
    return Payment::create([
      'order_id' => $orderId,
      'payment_method' => $paymentResult->paymentMethod,
      'transaction_id' => $paymentResult->transactionId,
      'amount' => $paymentResult->amount,
      'status' => $paymentResult->success ? PaymentStatus::SUCCESS : PaymentStatus::FAILED,
      'paid_at' => $paymentResult->paidAt,
    ]);
  }

  public function findByTransactionId(string $transactionId): ?Payment
  {
    return Payment::where('transaction_id', $transactionId)->first();
  }
}
