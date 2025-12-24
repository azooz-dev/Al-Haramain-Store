<?php

namespace Modules\User\Repositories\Interface;

use Modules\Review\Entities\Review\Review;

interface UserOrderItemReviewRepositoryInterface
{
  public function store(array $data): Review;

  public function checkItemIsInOrder($itemId, $orderId): bool;
}
