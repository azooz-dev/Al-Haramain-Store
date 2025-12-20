<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\Review\Entities\Review\Review;
use Modules\User\Repositories\Interface\UserOrderItemReviewRepositoryInterface;
use Modules\Order\Repositories\Interface\OrderItem\OrderItemRepositoryInterface;

class UserOrderItemReviewRepository implements UserOrderItemReviewRepositoryInterface
{
  public function __construct(
    private OrderItemRepositoryInterface $orderItemRepository
  ) {}

  public function store(array $data): Review
  {
    return Review::create($data);
  }

  public function checkItemIsInOrder($itemId, $orderId): bool
  {
    return $this->orderItemRepository->checkItemIsInOrder($itemId, $orderId);
  }
}
