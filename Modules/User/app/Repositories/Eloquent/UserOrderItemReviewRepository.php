<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\Review\Entities\Review\Review;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\User\Repositories\Interface\UserOrderItemReviewRepositoryInterface;

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
