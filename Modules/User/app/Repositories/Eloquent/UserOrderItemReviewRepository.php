<?php

namespace Modules\User\Repositories\Eloquent;

use App\Models\Review\Review;
use App\Models\Order\OrderItem;
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
