<?php

namespace App\Services\User\Order\Product\Review;

use App\Exceptions\Order\CheckDeliveredOrderException;
use App\Exceptions\Order\Review\ReviewException;
use App\Repositories\interface\User\Order\Product\Review\UserOrderProductReviewRepositoryInterface;
use App\Services\Order\OrderService;

use function App\Helpers\errorResponse;

class UserOrderProductReviewService
{
  public function __construct(
    private UserOrderProductReviewRepositoryInterface $userOrderProductReviewRepository,
    private OrderService $orderService
  ) {}

  public function storeAllOrderReviews(array $data, int $userId, int $orderId, int $productId)
  {
    try {
      $this->checkOrderDelivered($orderId);
      return $this->userOrderProductReviewRepository->store($data, $userId, $orderId, $productId);
    } catch (ReviewException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function checkOrderDelivered($orderId)
  {
    if (!$this->orderService->isDelivered($orderId)) {
      throw new CheckDeliveredOrderException(__('app.messages.order.order_undelivered'), 409);
    }

    return true;
  }
}
