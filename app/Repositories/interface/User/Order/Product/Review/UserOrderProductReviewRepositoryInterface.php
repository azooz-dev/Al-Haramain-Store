<?php

namespace App\Repositories\Interface\User\Order\Product\Review;

use App\Models\Review\Review;

interface UserOrderProductReviewRepositoryInterface
{
  public function store(array $data, int $userId, int $orderId, int $productId): Review;

  public function checkProductIsInOrder($productId, $orderId): bool;
}
