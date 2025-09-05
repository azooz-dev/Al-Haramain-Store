<?php

namespace App\Repositories\interface\Order\OrderItem;

use App\Models\Order\OrderItem;

interface OrderItemRepositoryInterface
{
  public function store(array $data, $orderId): OrderItem;
}
