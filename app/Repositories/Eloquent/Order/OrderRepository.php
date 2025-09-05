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
}
