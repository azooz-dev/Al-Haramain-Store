<?php

namespace App\Services\Order;

use App\Models\User\User;
use Illuminate\Support\Facades\DB;
use App\Services\Coupon\CouponService;
use function App\Helpers\errorResponse;
use App\Exceptions\Order\OrderException;
use App\Http\Resources\Order\OrderApiResource;
use App\Services\Product\Variant\ProductVariantService;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;
use App\Services\Offer\OfferService;

class OrderService
{
  private array $variantQuantities = [];

  public function __construct(
    private OrderRepositoryInterface $orderRepository,
    private OrderItemRepositoryInterface $orderItemRepository,
    private ProductVariantService $variantService,
    private CouponService $couponService,
    private OfferService $offerService
  ) {}

  /**
   * Store order and return Order resource or errorResponse array
   *
   * @param array $data
   * @return OrderApiResource|array
   */
  public function storeOrder(array $data)
  {
    try {
      // Validate basic input shape EARLY (assuming you've validated via FormRequest)
      if (empty($data['items']) || !is_array($data['items'])) {
        throw new OrderException(__('app.messages.order.invalid_items'), 422);
      }


      // Check buyer
      $this->checkBuyerVerified((int)$data['user_id']);

      foreach ($data['items'] as $item) {

        if ($item['orderable_type'] === "product") {

          // Group and reset
          $this->variantQuantities = $this->groupVariantQuantities($item);

          $this->variantQuantities['orderable_type'] = "product";
          $this->variantQuantities['orderable_id'] = $this->variantQuantities['product_id'];

          // Fetch all variants in one query (keyed by id)
          $variantIds = array_keys($this->variantQuantities);
          $variants = $this->variantService->getVariantsByIds($variantIds);

          // Check stock
          $this->variantService->checkStock($this->variantQuantities);

          // Calculate total price (variantService should accept grouped quantities + variants collection)
          $totalAmount = $this->variantService->calculateTotalOrderPrice($this->variantQuantities);

          // Apply coupon via CouponService (will validate usage counts)
          if (!empty($data['coupon_id'])) {
            $data['total_amount'] = $this->couponService->applyCouponToOrder((int)$data['coupon_id'], $totalAmount, (int)$data['user_id']);
          } else {
            $data['total_amount'] = $totalAmount;
          }

          // Use transaction and return resource from closure. Use retry for deadlocks (second param).
          $orderResource = DB::transaction(function () use ($data, $variants) {
            // create order
            $order = $this->orderRepository->store($data);
            if (!$order) {
              throw new OrderException(__('app.messages.order.order_error'), 500);
            }

            // Prepare order items payload
            $itemsPayload = [];
            foreach ($this->variantQuantities as $variantId => $item) {
              $variant = $variants->get($variantId);
              $price = $variant->effective_price;
              $itemsPayload[] = [
                'variant_id' => $variantId,
                'orderable_type' => $item['orderable_type'],
                'orderable_id' => $item['orderable_id'],
                'quantity' => $item['quantity'],
                'total_price' => $price * $item['quantity'],
              ];
            }

            // Bulk create order items using repository
            $created = $this->orderItemRepository->createMany($itemsPayload, $order->id);

            if (!$created) {
              throw new OrderException(__('app.messages.order.order_error'), 500);
            }

            // Atomically decrement stock for each variant; check affected rows
            foreach ($this->variantQuantities as $variantId => $item) {
              $affected = $this->variantService->decrementVariantStock($this->variantQuantities);
              if ($affected === 0) {
                // Not enough stock / concurrent sale — rollback and return conflict
                throw new OrderException(__('app.messages.order.insufficient_stock_for_variant', ['id' => $variantId]), 409);
              }
            }

            return new OrderApiResource($order);
          }, 5);
        } elseif ($item['orderable_type'] === 'offer') {

          $offerProducts = $this->offerService->getOfferProductForOrder($item['orderable_id']);

          $offerProducts = $this->groupVariantQuantities($offerProducts);

          $totalAmount = $this->variantService->calculateTotalOrderPrice($offerProducts);

          // Check stock
          $this->variantService->checkStock($offerProducts);

          // Calculate total price (variantService should accept grouped quantities + variants collection)
          $totalAmount = $this->variantService->calculateTotalOrderPrice($offerProducts);

          // Apply coupon via CouponService (will validate usage counts)
          if (!empty($data['coupon_id'])) {
            $data['total_amount'] = $this->couponService->applyCouponToOrder((int)$data['coupon_id'], $totalAmount, (int)$data['user_id']);
          } else {
            $data['total_amount'] = $totalAmount;
          }

          dd($data, $offerProducts);
          // Use transaction and return resource from closure. Use retry for deadlocks (second param).
          $orderResource = DB::transaction(function () use ($data, $offerProducts) {
            // create order
            $order = $this->orderRepository->store($data);
            if (!$order) {
              throw new OrderException(__('app.messages.order.order_error'), 500);
            }

            // Prepare order items payload
            $itemsPayload = [];
            foreach ($offerProducts as $item) {
              // $variant = $variants->get($variantId);
              // $price = $variant->effective_price;
              $itemsPayload[] = [
                'variant_id' => $item['variant_id'],
                'orderable_type' => $item['orderable_type'],
                'orderable_id' => $item['orderable_id'],
                'quantity' => $item['quantity'],
                'total_price' => $data[''],
              ];
            }

            // Bulk create order items using repository
            $created = $this->orderItemRepository->createMany($itemsPayload, $order->id);

            if (!$created) {
              throw new OrderException(__('app.messages.order.order_error'), 500);
            }

            // Atomically decrement stock for each variant; check affected rows
            foreach ($this->variantQuantities as $variantId => $item) {
              $affected = $this->variantService->decrementVariantStock($this->variantQuantities);
              if ($affected === 0) {
                // Not enough stock / concurrent sale — rollback and return conflict
                throw new OrderException(__('app.messages.order.insufficient_stock_for_variant', ['id' => $variantId]), 409);
              }
            }

            return new OrderApiResource($order);
          }, 5);
        }
      }

      return $orderResource;
    } catch (OrderException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function findOrderById(int $orderId)
  {
    try {
      $order = $this->orderRepository->show($orderId);

      return new OrderApiResource($order);
    } catch (OrderException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  /**
   * Pure function: group items and return assoc array keyed by variant_id.
   */
  private function groupVariantQuantities(array $items): array
  {
    $grouped = [];
    foreach ($items as $item) {
      $variantId = (int)$item['variant_id'];
      $qty = (int)($item['quantity'] ?? 0);
      if ($qty <= 0) {
        continue;
      }
      if (!isset($grouped[$variantId])) {
        $grouped[$variantId] = $item;
        $grouped[$variantId]['quantity'] = $qty;
      } else {
        $grouped[$variantId]['quantity'] += $qty;
      }
    }
    return $grouped;
  }

  private function checkBuyerVerified(int $userId): void
  {
    $buyer = User::find($userId);
    if (!$buyer) {
      throw new OrderException(__('app.messages.order.buyer_not_found'), 404);
    }
    if (!$buyer->verified) {
      throw new OrderException(__('app.messages.order.buyer_not_verified'), 403);
    }
  }


  public function isDelivered(int $orderId): bool
  {
    return $this->orderRepository->isDelivered($orderId);
  }
}
