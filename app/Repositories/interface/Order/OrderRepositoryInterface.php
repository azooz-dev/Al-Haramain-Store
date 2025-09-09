<?php

namespace App\Repositories\Interface\Order;

use App\Models\Order\Order;

interface OrderRepositoryInterface
{
  public function store(array $data): Order;

  public function countCouponUsage(int $couponId): int;

  public function countUserCouponUsage(int $couponId, int $userId): int;
}
