<?php

namespace App\Repositories\Eloquent\Order;

use App\Models\Order\Order;
use App\Repositories\Interface\Order\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
  public function store(array $data): Order
  {
    return Order::create($data);
  }

  public function countCouponUsage(int $couponId): int
  {
    return Order::where('coupon_id', $couponId)
      ->whereNotIn('status', [Order::CANCELLED, Order::REFUNDED])
      ->count();
  }

  public function countUserCouponUsage(int $couponId, int $userId): int
  {
    return Order::where('coupon_id', $couponId)
      ->where('user_id', $userId)
      ->whereNotIn('status', [Order::CANCELLED, Order::REFUNDED])
      ->count();
  }
}
