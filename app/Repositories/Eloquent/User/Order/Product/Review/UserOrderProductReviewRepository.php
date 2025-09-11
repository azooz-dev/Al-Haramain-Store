<?php

namespace App\Repositories\Eloquent\User\Order\Product\Review;

use App\Models\Review\Review;
use App\Models\Order\OrderItem;
use App\Repositories\interface\User\Order\Product\Review\UserOrderProductReviewRepositoryInterface;

class UserOrderProductReviewRepository implements UserOrderProductReviewRepositoryInterface
{
  public function store(array $data, int $userId, int $orderId, int $productId): Review
  {
    return Review::create([
      'order_id' => $orderId,
      'user_id' => $userId,
      'product_id' => $productId,
      'rating' => $data['rating'],
      'comment' => $data['comment'],
      'locale' => $data['locale']
    ]);
  }

  public function checkProductIsInOrder($productId, $orderId): bool
  {
    return OrderItem::where('order_id', $orderId)
      ->where('product_id', $productId)
      ->exists();
  }
}
