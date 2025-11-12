<?php

namespace App\Repositories\Interface\Payment;

use App\DTOs\PaymentResult;
use App\Models\Payment\Payment;

interface PaymentRepositoryInterface
{
  public function create(int $orderId, PaymentResult $paymentResult): Payment;

  public function findByTransactionId(string $transactionId): ?Payment;
}
