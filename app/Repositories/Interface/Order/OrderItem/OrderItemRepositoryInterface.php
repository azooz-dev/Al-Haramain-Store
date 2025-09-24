<?php

namespace App\Repositories\Interface\Order\OrderItem;


interface OrderItemRepositoryInterface
{
  public function createMany(array $itemsPayload, int $orderId): bool;
}
