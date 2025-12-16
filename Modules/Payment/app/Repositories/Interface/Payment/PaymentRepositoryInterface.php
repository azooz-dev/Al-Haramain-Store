<?php

namespace Modules\Payment\Repositories\Interface\Payment;

use Modules\Payment\DTOs\PaymentResult;
use Modules\Payment\Entities\Payment\Payment;

interface PaymentRepositoryInterface
{
  public function create(int $orderId, PaymentResult $paymentResult): Payment;

  public function findByTransactionId(string $transactionId): ?Payment;
}
