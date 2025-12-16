<?php

namespace App\Repositories\Eloquent\Payment;

use App\DTOs\PaymentResult;
use App\Models\Payment\Payment;
use App\Repositories\Interface\Payment\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
  public function create(int $orderId, PaymentResult $paymentResult): Payment
  {
    return Payment::create([
      'order_id' => $orderId,
      'payment_method' => $paymentResult->paymentMethod,
      'transaction_id' => $paymentResult->transactionId,
      'amount' => $paymentResult->amount,
      'status' => $paymentResult->success ? Payment::SUCCESS : Payment::FAILED,
      'paid_at' => $paymentResult->paidAt,
    ]);
  }

  public function findByTransactionId(string $transactionId): ?Payment
  {
    return Payment::where('transaction_id', $transactionId)->first();
  }
}
