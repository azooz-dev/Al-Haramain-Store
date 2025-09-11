<?php

namespace App\Services\Order;

use Throwable;
use App\Models\User\User;
use App\Models\Order\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Coupon\CouponService;
use function App\Helpers\errorResponse;
use App\Exceptions\Order\OrderException;
use App\Http\Resources\Order\OrderApiResource;
use App\Services\Product\Variant\ProductVariantService;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;

class OrderService
{
  private array $variantQuantities = [];

  public function __construct(
    private OrderRepositoryInterface $orderRepository,
    private OrderItemRepositoryInterface $orderItemRepository,
    private ProductVariantService $variantService,
    private CouponService $couponService
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

      // Group and reset
      $this->variantQuantities = $this->groupVariantQuantities($data['items']);

      // Fetch all variants in one query (keyed by id)
      $variantIds = array_keys($this->variantQuantities);
      $variants = $this->variantService->getVariantsByIds($variantIds);

      // Check buyer
      $this->checkBuyerVerified((int)$data['user_id']);

      // Check stock
      $this->variantService->checkStock($this->variantQuantities);

      // Ensure variants exist and have stock before transaction
      $this->ensureVariantsExistAndStock($this->variantQuantities, $variants);

      // Calculate total price (variantService should accept grouped quantities + variants collection)
      $totalAmount = $this->variantService->calculateTotalOrderPrice($this->variantQuantities, $variants);

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
          $price = $variant->amount_discount_price ?? $variant->price;
          $itemsPayload[] = [
            'variant_id' => $variantId,
            'product_id' => $variant->product_id,
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

      // Dispatch events / jobs here if needed (outside transaction)
      return $orderResource;
    } catch (OrderException $e) {
      return errorResponse($e->getMessage(), $e->getCode() ?: 400);
    } catch (Throwable $e) {
      Log::error('OrderService::storeOrder failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
      return errorResponse(__('app.messages.order.order_error'), 500);
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

  /**
   * Ensure every variant exists and has enough stock (pre-check)
   *
   * @param array $variantQuantities keyed by id
   * @param \Illuminate\Support\Collection $variants keyed by id
   * @throws OrderException
   */
  private function ensureVariantsExistAndStock(array $variantQuantities, $variants): void
  {
    foreach ($variantQuantities as $variantId => $item) {
      $variant = $variants->get((int)$variantId);
      if (!$variant) {
        throw new OrderException(__('app.messages.order.variant_not_found', ['id' => $variantId]), 404);
      }
      if ($variant->quantity < (int)$item['quantity']) {
        throw new OrderException(__('app.messages.order.insufficient_stock_for_variant', ['id' => $variantId]), 409);
      }
    }
  }

  public function isDelivered(int $orderId): bool
  {
    return $this->orderRepository->isDelivered($orderId);
  }
}
