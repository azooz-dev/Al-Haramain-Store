<?php

namespace Modules\User\Services;

use App\Models\User\User;
use App\Exceptions\Order\OrderException;
use App\Http\Resources\Order\OrderApiResource;
use App\Repositories\Interface\User\Order\UserOrderRepositoryInterface;

use function App\Helpers\errorResponse;

class UserOrderService
{
  public function __construct(private UserOrderRepositoryInterface $userOrderRepository) {}

  public function getAllUserOrders(int $userId)
  {
    try {
      $this->checkUserVerified($userId);
      $userOrders = $this->userOrderRepository->getAllUserOrders($userId);

      if (!$userOrders) {
        return errorResponse(__("app.messages.order.order_not_found"), 404);
      }

      return OrderApiResource::collection($userOrders);
    } catch (OrderException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  private function checkUserVerified(int $userId): void
  {
    $user = User::find($userId);
    if (!$user) {
      throw new OrderException(__('app.messages.order.user_not_found'), 404);
    }
    if (!$user->verified) {
      throw new OrderException(__('app.messages.order.user_not_verified'), 403);
    }
  }
}
