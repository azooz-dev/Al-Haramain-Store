<?php

namespace App\DTOs;

use Carbon\Carbon;

class PaymentResult
{
  public function __construct(
    public bool $success,
    public ?string $transactionId,
    public string $paymentMethod,
    public float $amount,
    public ?Carbon $paidAt,
  ) {}
}
