<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\Order\Entities\Order\Order;
use Modules\User\Repositories\Interface\UserOrderRepositoryInterface;

class UserOrderRepository implements UserOrderRepositoryInterface
{
  public function getAllUserOrders(int $userId)
  {
    return Order::where('user_id', $userId)->get();
  }
}
