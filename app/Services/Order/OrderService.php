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
            $this->validateOrderInput($data);
            $this->checkBuyerVerified((int)$data['user_id']);
            $this->groupedOrderItems = $this->groupOrderItemsByTypeAndId($data['items']);

            $this->processProductItems();

            $this->processOfferItems();

            $data['items'] = $this->calculateItemPrices($data['items']);
            $data['total_amount'] = $this->calculateTotalAmount($data);

            // Use transaction and return resource from closure. Use retry for deadlocks (second param).
            $orderResource = DB::transaction(function () use ($data) {
                return $this->createOrderAndItems($data);
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
     * For products, groups by variant_id instead of orderable_id.
     *
     * @param array $items
     * @return array
     */
    public static function groupOrderItemsByTypeAndId(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            if (!isset($item['orderable_type'])) {
                // skip invalid items
                continue;
            }

            $type = $item['orderable_type'];

            // For products, use variant_id; for others, use orderable_id
            if ($type === 'product') {
                if (!isset($item['variant_id'])) {
                    // skip invalid product items without variant_id
                    continue;
                }
                $id = $item['variant_id'];
            } else {
                if (!isset($item['orderable_id'])) {
                    // skip invalid items without orderable_id
                    continue;
                }
                $id = $item['orderable_id'];
            }

            // Preserve all keys (like color_id, variant_id, etc.)
            $grouped[$type][$id] = $item;
        }
        return $grouped;
    }

    /**
     * Validate order input data
     *
     * @param array $data
     * @throws OrderException
     */
    private function validateOrderInput(array $data): void
    {
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new OrderException(__('app.messages.order.invalid_items'), 422);
        }
    }

    /**
     * Process product items - fetch variants and check stock
     */
    private function processProductItems(): void
    {
        if (!isset($this->groupedOrderItems['product'])) {
            return;
        }

        // Fetch all variants in one query (keyed by id)
        $variantIds = array_keys($this->groupedOrderItems['product']);
        $this->variants = $this->variantService->getVariantsByIds($variantIds);

        // Check stock
        $this->variantService->checkStock($this->groupedOrderItems['product']);
    }

    /**
     * Process offer items - fetch offers and check stock for offer variants
     */
    private function processOfferItems(): void
    {
        if (!isset($this->groupedOrderItems['offer'])) {
            return;
        }

        $offerIds = array_keys($this->groupedOrderItems['offer']);
        $this->offers = $this->offerService->getOffersByIds($offerIds);

        // Transform offer items to variant-based structure for stock validation
        $offerVariantsForValidation = $this->buildOfferVariantsForValidation();

        // Check stock for offer variants
        if (!empty($offerVariantsForValidation)) {
            $this->variantService->checkStock($offerVariantsForValidation);
        }
    }

    /**
     * Build offer variants array for stock validation
     *
     * @return array
     */
    private function buildOfferVariantsForValidation(): array
    {
        $offerVariantsForValidation = [];

        foreach ($this->groupedOrderItems['offer'] as $offerId => $offerItem) {
            $offer = $this->offers->get($offerId);
            if (!$offer) {
                continue;
            }

            foreach ($offer->offerProducts as $offerProduct) {
                $variantId = $offerProduct->product_variant_id;
                if (!isset($offerVariantsForValidation[$variantId])) {
                    $offerVariantsForValidation[$variantId] = [
                        'quantity' => $offerProduct->quantity * $offerItem['quantity'],
                        'variant_id' => $variantId,
                        'color_id' => $offerProduct->product_color_id,
                    ];
                } else {
                    $offerVariantsForValidation[$variantId]['quantity'] += $offerProduct->quantity * $offerItem['quantity'];
                }
            }
        }

        return $offerVariantsForValidation;
    }

    /**
     * Calculate prices for all order items
     *
     * @param array $items
     * @return array
     */
    private function calculateItemPrices(array $items): array
    {
        $newItems = [];

        foreach ($items as $item) {
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

        return $newItems;
    }

    /**
     * Calculate total amount and apply coupon if provided
     *
     * @param array $data
     * @return float
     */
    private function calculateTotalAmount(array $data): float
    {
        $totalAmount = $this->variantService->calculateTotalOrderPrice($data['items']);

        // Apply coupon via CouponService (will validate usage counts)
        if (!empty($data['coupon_id'])) {
            return $this->couponService->applyCouponToOrder((int)$data['coupon_id'], $totalAmount, (int)$data['user_id']);
        }

        return $totalAmount;
    }

    /**
     * Create order and order items, then update stock
     *
     * @param array $data
     * @return OrderApiResource
     * @throws OrderException
     */
    private function createOrderAndItems(array $data): OrderApiResource
    {
        // Create order
        $order = $this->orderRepository->store($data);
        if (!$order) {
            throw new OrderException(__('app.messages.order.order_error'), 500);
        }

        // Bulk create order items using repository
        $created = $this->orderItemRepository->createMany($data['items'], $order->id);
        if (!$created) {
            throw new OrderException(__('app.messages.order.order_error'), 500);
        }

        // Update stock
        $this->updateStockAfterOrder();

        return new OrderApiResource($order);
    }

    /**
     * Update stock after successful order creation
     */
    private function updateStockAfterOrder(): void
    {
        if (isset($this->offers)) {
            // Transform offer products to match expected structure
            $offerProductsArray = $this->buildOfferProductsForStockUpdate();
            $all = ($this->groupedOrderItems['product'] ?? []) + $offerProductsArray;
            $this->variantService->decrementVariantStock($all);
        } else {
            $this->variantService->decrementVariantStock($this->groupedOrderItems['product'] ?? []);
        }
    }

    /**
     * Build offer products array for stock update
     *
     * @return array
     */
    private function buildOfferProductsForStockUpdate(): array
    {
        return $this->offers->flatMap(function ($offer) {
            return $offer->offerProducts;
        })->mapWithKeys(function ($offerProduct) {
            return [
                $offerProduct->product_variant_id => [
                    'quantity' => $offerProduct->quantity,
                    'variant_id' => $offerProduct->product_variant_id,
                    'color_id' => $offerProduct->product_color_id,
                    'orderable_type' => 'offer',
                    'orderable_id' => $offerProduct->offer_id,
                ]
            ];
        })->toArray();
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
