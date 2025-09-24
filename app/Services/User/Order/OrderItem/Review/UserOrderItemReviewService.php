<?php

namespace App\Services\User\Order\OrderItem\Review;

use App\Models\Order\OrderItem;
use App\Services\Order\OrderService;
use function App\Helpers\errorResponse;
use App\Exceptions\Order\Review\ReviewException;

use App\Exceptions\Order\CheckDeliveredOrderException;
use App\Repositories\Interface\User\Order\OrderItem\Review\UserOrderItemReviewRepositoryInterface;

class UserOrderItemReviewService
{
  public function __construct(
    private UserOrderItemReviewRepositoryInterface $userOrderItemReviewRepository,
    private OrderService $orderService
  ) {}

  public function storeAllOrderReviews(array $data, int $userId, int $orderId, OrderItem $item)
  {
    try {
      $this->checkOrderDelivered($orderId);
      $this->checkItemIsInOrder($item->id, $orderId);

      $data = array_merge($data, [
        'user_id' => $userId,
        'order_id' => $orderId,
        'orderable_id' => $item->orderable_id,
        'orderable_type' => $item->orderable_type
      ]);
      return $this->userOrderItemReviewRepository->store($data);
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

  public function checkItemIsInOrder($itemId, $orderId)
  {
    $isInOrder = $this->userOrderItemReviewRepository->checkItemIsInOrder($itemId, $orderId);

    if (!$isInOrder) {
      throw new ReviewException(__('app.messages.review.product_not_in_order'), 409);
    }

    return true;
  }
}
