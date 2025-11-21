<?php

namespace App\Services\Order;

use App\Models\User\User;
use App\Models\Offer\Offer;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Offer\OfferService;
use App\Services\Coupon\CouponService;
use function App\Helpers\errorResponse;
use App\Exceptions\Order\OrderException;
use App\Services\Payment\PaymentService;
use App\Http\Resources\Order\OrderApiResource;
use App\Services\Product\Variant\ProductVariantService;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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
        private OfferService $offerService,
        private PaymentService $paymentService
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
            $this->checkBuyerVerified((int)$data['user_id']);
            $this->groupedOrderItems = $this->groupOrderItemsByTypeAndId($data['items']);

            $this->processProductItems();

            $this->processOfferItems();

            $data['items'] = $this->calculateItemPrices($data['items']);
            $data['total_amount'] = $this->calculateTotalAmount($data);

            // Use transaction and return resource from closure. Use retry for deadlocks (second param).
            $orderResource = DB::transaction(function () use ($data) {
                // Process payment
                $paymentResult = $this->paymentService->processPayment($data);

                // Create order and items
                $order = $this->createOrderAndItems($data);
                if ($data['payment_method'] === Order::PAYMENT_METHOD_CREDIT_CARD && $paymentResult->transactionId) {
                    // Create payment record
                    $this->paymentService->createPayment($order->id, $paymentResult);
                }
                return $order;
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
            if ($type === Product::class) {
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
     * Process product items - fetch variants and check stock
     */
    private function processProductItems(): void
    {
        if (!isset($this->groupedOrderItems[Product::class])) {
            return;
        }

        // Fetch all variants in one query (keyed by id)
        $variantIds = array_keys($this->groupedOrderItems[Product::class]);
        $this->variants = $this->variantService->getVariantsByIds($variantIds);

        // Check stock
        $this->variantService->checkStock($this->groupedOrderItems[Product::class]);
    }

    /**
     * Process offer items - fetch offers and check stock for offer variants
     */
    private function processOfferItems(): void
    {
        if (!isset($this->groupedOrderItems[Offer::class])) {
            return;
        }

        $offerIds = array_keys($this->groupedOrderItems[Offer::class]);
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

        foreach ($this->groupedOrderItems[Offer::class] as $offerId => $offerItem) {
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
            if ($item['orderable_type'] === Product::class) {
                $variant = $this->variants->get($item['variant_id']);
                $item['total_price'] = $variant->effective_price * $item['quantity'];
            } else if ($item['orderable_type'] === Offer::class) {
                $offer = $this->offers->get($item['orderable_id']);
                $item['total_price'] = $offer->offer_price * $item['quantity'];
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
        if (!empty($data['coupon_code'])) {
            return $this->couponService->applyCouponToOrder($data['coupon_code'], $totalAmount, (int)$data['user_id']);
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
            $all = ($this->groupedOrderItems[Product::class] ?? []) + $offerProductsArray;
            $this->variantService->decrementVariantStock($all);
        } else {
            $this->variantService->decrementVariantStock($this->groupedOrderItems[Product::class] ?? []);
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

    public function updateOrder(int $id, array $data): Order
    {
        // If status is being updated, use updateOrderStatus for logging
        if (isset($data['status'])) {
            return $this->updateOrderStatus($id, $data['status']);
        }

        // Otherwise, update normally
        return $this->orderRepository->update($id, $data);
    }

    public function updateOrderStatus(int $id, string $status): Order
    {
        $order = $this->orderRepository->findById($id);

        // Validate status transition
        if (!$this->canUpdateStatus($order, $status)) {
            throw new OrderException(__('app.messages.order.invalid_status_transition'), 422);
        }

        $oldStatus = $order->status;

        // Update order status via repository
        $updatedOrder = $this->orderRepository->updateStatus($id, $status);

        // Log status change
        Log::info('Order status changed', [
            'order_id' => $id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $status,
            'user_id' => Auth::id(),
        ]);

        return $updatedOrder;
    }

    public function deleteOrder(int $id): bool
    {
        $order = $this->orderRepository->findById($id);

        // Check if order can be deleted
        if (!$this->canDeleteOrder($order)) {
            throw new OrderException(__('app.messages.order.cannot_delete'), 422);
        }

        return $this->orderRepository->delete($id);
    }

    public function markOrdersAsProcessing(array $ids): int
    {
        return Order::whereIn('id', $ids)->update(['status' => Order::PROCESSING]);
    }

    public function markOrdersAsShipped(array $ids): int
    {
        return Order::whereIn('id', $ids)->update(['status' => Order::SHIPPED]);
    }

    public function getOrdersCount(): int
    {
        return $this->orderRepository->count();
    }

    public function getOrdersCountByStatus(string $status): int
    {
        return $this->orderRepository->countByStatus($status);
    }

    public function getQueryBuilder(): Builder
    {
        return $this->orderRepository->getQueryBuilder();
    }

    public function getPaymentStatus(Order $order): string
    {
        // Use model accessor
        return $order->payment_status;
    }

    public function canDeleteOrder(Order $order): bool
    {
        // Only cancelled or refunded orders can be deleted
        return in_array($order->status, [Order::CANCELLED, Order::REFUNDED]);
    }

    protected function getStatusTransitions(): array
    {
        return [
            Order::PENDING => [
                Order::PROCESSING,
                Order::SHIPPED,
                Order::CANCELLED,
            ],
            Order::PROCESSING => [
                Order::SHIPPED,
                Order::CANCELLED,
            ],
            Order::SHIPPED => [
                Order::DELIVERED,
                Order::CANCELLED,
            ],
            Order::DELIVERED => [
                Order::REFUNDED,
            ],
            Order::CANCELLED => [
                // Terminal state - no transitions allowed
            ],
            Order::REFUNDED => [
                // Terminal state - no transitions allowed
            ],
        ];
    }

    public function canUpdateStatus(Order $order, string $newStatus): bool
    {
        // Check if new status is valid
        if (!in_array($newStatus, Order::getStatuses())) {
            return false;
        }

        // If status hasn't changed, allow it
        if ($order->status === $newStatus) {
            return true;
        }

        $transitions = $this->getStatusTransitions();
        $currentStatus = $order->status;

        if (!isset($transitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, $transitions[$currentStatus]);
    }

    public function getAvailableStatuses(Order $order): array
    {
        $currentStatus = $order->status;
        $transitions = $this->getStatusTransitions();
        $availableStatuses = $transitions[$currentStatus] ?? [];

        // Always include current status (user can keep it the same)
        $availableStatuses[] = $currentStatus;
        $availableStatuses = array_unique($availableStatuses);

        // Build options array with translations
        $options = [];
        foreach ($availableStatuses as $status) {
            $options[$status] = match ($status) {
                Order::PENDING => __('app.status.pending'),
                Order::PROCESSING => __('app.status.processing'),
                Order::SHIPPED => __('app.status.shipped'),
                Order::DELIVERED => __('app.status.delivered'),
                Order::CANCELLED => __('app.status.cancelled'),
                Order::REFUNDED => __('app.status.refunded'),
                default => $status,
            };
        }

        return $options;
    }
}
