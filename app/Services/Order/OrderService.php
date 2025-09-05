<?php

namespace App\Services\Order;

use App\Models\User\User;
use function App\Helpers\errorResponse;
use App\Exceptions\Order\OrderException;
use App\Http\Resources\Order\OrderApiResource;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\interface\Order\OrderItem\OrderItemRepositoryInterface;
use App\Services\Product\Variant\ProductVariantService;

class OrderService
{
  public function __construct(
    private OrderRepositoryInterface $orderRepository,
    private OrderItemRepositoryInterface $orderItemRepository,
    private ProductVariantService $variantService
  ) {}

  private $variantQuantities = [];

  public function storeOrder(array $data)
  {
    try {
      $this->checkBuyerVerified($data['user_id']);
      $this->groupVariantQuantities($data['items']);
      $data['total_amount'] = $this->variantService->calculateTotalOrderPrice($this->variantQuantities);

      $order = $this->orderRepository->store($data);

      if (!$order) {
        throw new OrderException(__('app.messages.order.order_error'));
      }

      $orderItems = $this->storeOrderItems($this->variantQuantities, $order->id);

      if (!$orderItems) {
        throw new OrderException(__('app.messages.order.order_error'));
      }

      return new OrderApiResource($order);
    } catch (OrderException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }


  private function storeOrderItems(array $data, $orderId)
  {
    try {
      foreach ($data as $item) {
        $orderItems = $this->orderItemRepository->store($item, $orderId);
      }
      $this->variantService->decrementVariantStock($this->variantQuantities);

      return $orderItems;
    } catch (OrderException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  private function checkBuyerVerified($userId)
  {
    $buyer = User::findOrFail($userId);
    if (!$buyer->verified) {
      throw new OrderException(__('app.messages.order.buyer_not_verified'), 403);
    }
  }

  public function groupVariantQuantities($items)
  {
    foreach ($items as $item) {
      $variantId = $item['variant_id'];
      if (!isset($this->variantQuantities[$variantId])) {
        // First item
        $this->variantQuantities[$variantId] = $item;
      } else {
        // Already Exists
        $this->variantQuantities[$variantId]['quantity'] += $item['quantity'];
      }
    }

    // Re-index to a numerically indexed array
    return array_values($this->variantQuantities);
  }
}
