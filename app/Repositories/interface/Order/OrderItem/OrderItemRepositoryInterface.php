<?php

namespace App\Repositories\Interface\Order\OrderItem;

use App\Models\Order\OrderItem;

interface OrderItemRepositoryInterface
{
  public function store(array $data, $orderId): OrderItem;

  public function createMany(array $itemsPayload, int $orderId): bool;
}
