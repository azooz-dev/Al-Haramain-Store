<?php

namespace App\Repositories\Eloquent\User\Order;

use App\Models\Order\Order;
use App\Repositories\Interface\User\Order\UserOrderRepositoryInterface;

class UserOrderRepository implements UserOrderRepositoryInterface
{
  public function getAllUserOrders(int $userId)
  {
    return Order::where('user_id', $userId)->get();
  }
}
