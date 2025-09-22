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
  private array $groupedOrderItems = [];
  private $variants;
  private $offers;

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

      $this->groupedOrderItems = $this->groupOrderItemsByTypeAndId($data['items']);


      if (isset($this->groupedOrderItems['product'])) {

        // Fetch all variants in one query (keyed by id)
        $variantIds = array_keys($this->groupedOrderItems['product']);
        $this->variants = $this->variantService->getVariantsByIds($variantIds);

        // Check stock
        $this->variantService->checkStock($this->groupedOrderItems['product']);
      }

      if (isset($this->groupedOrderItems['offer'])) {

        $offerIds = array_keys($this->groupedOrderItems['offer']);
        $this->offers = $this->offerService->getOffersByIds($offerIds);

        // Check stock
        $this->variantService->checkStock($this->groupedOrderItems['offer']);
      }

      $newItems = [];
      foreach ($data['items'] as $item) {
        if ($item['orderable_type'] === 'product') {
          $variant = $this->variants->get($item['variant_id']);
          $item['total_price'] = $variant->effective_price * $item['quantity'];
        } else if ($item['orderable_type'] === 'offer') {
          $offer = $this->offers->get($item['orderable_id']);
          $item['total_price'] = $offer->offer_price;
          // Ensure offer items have the same structure as product items
          $item['variant_id'] = $item['variant_id'] ?? null;
          $item['color_id'] = $item['color_id'] ?? null;
        }
        $newItems[] = $item;
      }
      $data['items'] = $newItems;

      // // Calculate total price (variantService should accept grouped quantities + variants collection)
      $totalAmount = $this->variantService->calculateTotalOrderPrice($data['items']);

      // Apply coupon via CouponService (will validate usage counts)
      if (!empty($data['coupon_id'])) {
        $data['total_amount'] = $this->couponService->applyCouponToOrder((int)$data['coupon_id'], $totalAmount, (int)$data['user_id']);
      } else {
        $data['total_amount'] = $totalAmount;
      }

      // Use transaction and return resource from closure. Use retry for deadlocks (second param).
      $orderResource = DB::transaction(function () use ($data) {
        // create order
        $order = $this->orderRepository->store($data);
        if (!$order) {
          throw new OrderException(__('app.messages.order.order_error'), 500);
        }

        // Bulk create order items using repository
        $created = $this->orderItemRepository->createMany($data['items'], $order->id);

        if (!$created) {
          throw new OrderException(__('app.messages.order.order_error'), 500);
        }

        // Atomically decrement stock for each variant; check affected rows
        $allOfferProducts = $this->offers->flatMap(function ($offer) {
          return $offer->offerProducts;
        });

        $all = array_merge($this->variants->toArray(), $allOfferProducts->toArray());
        $all = $this->groupVariantQuantities($all);
        $this->variantService->decrementVariantStock($all);

        return new OrderApiResource($order);
      }, 5);

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
      $variantId = (int) isset($item['product_variant_id']) ? (int) $item['product_variant_id'] : (int) $item['id'];
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

  /**
   * Group order items by orderable_type and orderable_id for efficient access.
   *
   * @param array $items
   * @return array
   */
  public static function groupOrderItemsByTypeAndId(array $items): array
  {
    $grouped = [];
    foreach ($items as $item) {
      if (!isset($item['orderable_type'], $item['orderable_id'])) {
        // skip invalid items
        continue;
      }
      $type = $item['orderable_type'];
      $id = $item['orderable_id'];
      // For products, preserve all keys (like color_id, variant_id, etc.)
      $grouped[$type][$id] = $item;
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
