<?php

namespace App\Services\Order\Pipeline;

use App\Models\Order\Order;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;
use App\Services\Product\Variant\ProductVariantService;
use App\Services\Payment\PaymentService;
use App\Exceptions\Order\OrderException;
use App\Http\Resources\Order\OrderApiResource;

class CreateOrderStep implements OrderProcessingStep
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderItemRepositoryInterface $orderItemRepository,
        private ProductVariantService $variantService,
        private PaymentService $paymentService
    ) {}

    public function handle(array $data, \Closure $next)
    {
        // Create order
        $order = $this->orderRepository->store($data);
        if (!$order) {
            throw new OrderException(__('app.messages.order.order_error'), 500);
        }

        // Create order items
        $created = $this->orderItemRepository->createMany($data['items'], $order->id);
        if (!$created) {
            throw new OrderException(__('app.messages.order.order_error'), 500);
        }

        // Update stock
        $this->updateStock($data);

        // Create payment record if needed
        $paymentResult = $data['_payment_result'] ?? null;
        if ($data['payment_method'] === Order::PAYMENT_METHOD_CREDIT_CARD && $paymentResult?->transactionId) {
            $this->paymentService->createPayment($order->id, $paymentResult);
        }

        // Store order in data for next step
        $data['_order'] = $order;

        return $next($data);
    }

    private function updateStock(array $data): void
    {
        $groupedItems = $data['_grouped_items'];
        $offers = $data['_offers'] ?? collect();

        if ($offers->isNotEmpty()) {
            $offerProducts = $this->buildOfferProductsForStockUpdate($offers);
            $all = ($groupedItems[\App\Models\Product\Product::class] ?? []) + $offerProducts;
            $this->variantService->decrementVariantStock($all);
        } else {
            $this->variantService->decrementVariantStock($groupedItems[\App\Models\Product\Product::class] ?? []);
        }
    }

    private function buildOfferProductsForStockUpdate($offers): array
    {
        return $offers->flatMap(function ($offer) {
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
}


