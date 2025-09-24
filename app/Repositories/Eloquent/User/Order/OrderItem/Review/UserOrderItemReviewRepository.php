<?php

namespace App\Repositories\Eloquent\User\Order\OrderItem\Review;

use App\Models\Review\Review;
use App\Models\Order\OrderItem;
use App\Repositories\Interface\User\Order\OrderItem\Review\UserOrderItemReviewRepositoryInterface;

class UserOrderItemReviewRepository implements UserOrderItemReviewRepositoryInterface
{
  public function store(array $data): Review
  {
    return Review::create($data);
  }

  public function checkItemIsInOrder($itemId, $orderId): bool
  {
    return OrderItem::where('order_id', $orderId)
      ->where('id', $itemId)
      ->exists();
  }
}
