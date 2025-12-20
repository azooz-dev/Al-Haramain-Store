<?php

namespace Modules\Order\Repositories\Interface\OrderItem;


interface OrderItemRepositoryInterface
{
  public function createMany(array $itemsPayload, int $orderId): bool;

  public function update(int $itemId, array $data): bool;

  public function checkItemIsReviewed($itemId): bool;

  public function checkItemIsInOrder(int $itemId, int $orderId): bool;
}
