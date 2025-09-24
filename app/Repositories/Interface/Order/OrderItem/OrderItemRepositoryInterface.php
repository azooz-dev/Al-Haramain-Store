<?php

namespace App\Repositories\Interface\Order\OrderItem;


interface OrderItemRepositoryInterface
{
  public function createMany(array $itemsPayload, int $orderId): bool;

  public function update(int $itemId, array $data): bool;

  public function checkItemIsReviewed($itemId): bool;
}
