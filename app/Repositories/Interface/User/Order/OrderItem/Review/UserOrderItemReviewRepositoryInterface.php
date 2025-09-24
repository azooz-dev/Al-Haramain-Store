<?php

namespace App\Repositories\Interface\User\Order\OrderItem\Review;

use App\Models\Review\Review;

interface UserOrderItemReviewRepositoryInterface
{
  public function store(array $data): Review;

  public function checkItemIsInOrder($itemId, $orderId): bool;
}
