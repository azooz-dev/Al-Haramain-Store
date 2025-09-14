<?php

namespace App\Repositories\Interface\User\Order;

interface UserOrderRepositoryInterface
{
  public function getAllUserOrders(int $userId);
}
