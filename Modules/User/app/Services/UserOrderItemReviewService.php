<?php

namespace Modules\User\Services;

use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Order\Contracts\OrderServiceInterface;
use function App\Helpers\errorResponse;
use Modules\Order\Exceptions\Order\Review\ReviewException;

use Modules\Review\Http\Resources\Review\ReviewApiResource;
use Modules\Order\Exceptions\Order\CheckDeliveredOrderException;
use Modules\User\Contracts\UserOrderItemReviewServiceInterface;
use Modules\Order\Repositories\Interface\OrderItem\OrderItemRepositoryInterface;
use Modules\User\Repositories\Interface\UserOrderItemReviewRepositoryInterface;

class UserOrderItemReviewService implements UserOrderItemReviewServiceInterface
{
  public function __construct(
    private UserOrderItemReviewRepositoryInterface $userOrderItemReviewRepository,
    private OrderServiceInterface $orderService,
    private OrderItemRepositoryInterface $orderItemRepository
  ) {}

  public function storeAllOrderReviews(array $data, int $userId, int $orderId, int $itemId)
  {
    try {
      $this->checkItemIsReviewed($itemId);
      $this->checkOrderDelivered($orderId);
      $this->checkItemIsInOrder($itemId, $orderId);

      $data = array_merge($data, [
        'user_id' => $userId,
        'order_id' => $orderId,
        'order_item_id' => $itemId,
      ]);

      $review = $this->userOrderItemReviewRepository->store($data);

      if ($review) {
        $this->orderItemRepository->update($itemId, ['is_reviewed' => true]);
      }

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
      throw new ReviewException(__('app.messages.review.item_not_in_order'), 409);
    }

    return true;
  }

  public function checkItemIsReviewed($itemId)
  {
    $isReviewed = $this->orderItemRepository->checkItemIsReviewed($itemId);

    if ($isReviewed) {
      throw new ReviewException(__('app.messages.review.item_already_reviewed'), 409);
    }

    return true;
  }
}
