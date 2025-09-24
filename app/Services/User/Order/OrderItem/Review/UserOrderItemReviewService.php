<?php

namespace App\Services\User\Order\OrderItem\Review;

use App\Models\Order\OrderItem;
use App\Services\Order\OrderService;
use function App\Helpers\errorResponse;
use App\Exceptions\Order\Review\ReviewException;

use App\Http\Resources\Review\ReviewApiResource;
use App\Exceptions\Order\CheckDeliveredOrderException;
use App\Repositories\Interface\User\Order\OrderItem\Review\UserOrderItemReviewRepositoryInterface;

class UserOrderItemReviewService
{
  public function __construct(
    private UserOrderItemReviewRepositoryInterface $userOrderItemReviewRepository,
    private OrderService $orderService
  ) {}

  public function storeAllOrderReviews(array $data, int $userId, int $orderId, int $itemId)
  {
    try {
      $this->checkOrderDelivered($orderId);
      $this->checkItemIsInOrder($itemId, $orderId);

      $data = array_merge($data, [
        'user_id' => $userId,
        'order_id' => $orderId,
        'order_item_id' => $itemId,
      ]);

      $review = $this->userOrderItemReviewRepository->store($data);

      return new ReviewApiResource($review);
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
