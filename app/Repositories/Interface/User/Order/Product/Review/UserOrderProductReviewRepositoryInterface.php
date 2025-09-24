<?php

namespace App\Repositories\Interface\User\Order\OrderItem\Review;

use App\Models\Review\Review;

interface UserOrderItemReviewRepositoryInterface
{
  public function store(array $data, int $userId, int $orderId, int $productId): Review;

  public function checkProductIsInOrder($productId, $orderId): bool;
}
