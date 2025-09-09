<?php

namespace App\Services\Order;

use App\Models\User\User;
use App\Models\Coupon\Coupon;
use Illuminate\Support\Facades\DB;
use function App\Helpers\errorResponse;
use App\Exceptions\Order\OrderException;
use App\Http\Resources\Order\OrderApiResource;
use App\Services\Product\Variant\ProductVariantService;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;

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
      $order = DB::transaction(function () use ($data) {

        $this->checkBuyerVerified($data['user_id']);

        $this->groupVariantQuantities($data['items']);
        $totalAmount = $this->variantService->calculateTotalOrderPrice($this->variantQuantities);

        if ($data['coupon_id']) {
          $data['total_amount'] = $this->checkCouponValidation($data['coupon_id'], $totalAmount);
        } else {
          $data['total_amount'] = $totalAmount;
        }

        $order = $this->orderRepository->store($data);

        if (!$order) {
          throw new OrderException(__('app.messages.order.order_error'));
        }

        $orderItems = $this->storeOrderItems($this->variantQuantities, $order->id);

        if (!$orderItems) {
          throw new OrderException(__('app.messages.order.order_error'));
        }

        return $order;
      });

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

  private function checkCouponValidation($couponId, $totalAmount)
  {
    $coupon = Coupon::find($couponId);
    if (!$coupon) {
      throw new OrderException(__('app.messages.order.coupon_not_found'), 404);
    }

    if ($coupon->status == Coupon::INACTIVE) {
      throw new OrderException(__('app.messages.order.coupon_inactive'), 400);
    }

    if ($coupon->start_date > now()) {
      throw new OrderException(__('app.messages.order.coupon_not_started'), 400);
    }

    if ($coupon->end_date < now()) {
      throw new OrderException(__('app.messages.order.coupon_expired'), 400);
    }

    if ($coupon->usage_limit <= $coupon->usage_limit_per_user) {
      throw new OrderException(__('app.messages.order.coupon_usage_limit_exceeded', ['usage_limit' => $coupon->usage_limit]), 400);
    }

    if ($coupon->usage_limit_per_user <= $coupon->usage_limit) {
      throw new OrderException(__('app.messages.order.coupon_usage_limit_exceeded', ['usage_limit_per_user' => $coupon->usage_limit_per_user]), 400);
    }

    $discountType = $coupon->discount_type;
    $discountAmount = $coupon->discount_amount;
    $totalAmount = $totalAmount;
    $discountAmount = $discountType === Coupon::FIXED ? $discountAmount : $totalAmount * $discountAmount / 100;
    $totalAmount = $totalAmount - $discountAmount;
    return $totalAmount;
  }
}
