<?php

namespace Modules\Order\Services\Order;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use function App\Helpers\errorResponse;
use Modules\Order\Events\OrderCreated;
use Modules\Order\Events\OrderStatusChanged;
use Modules\Order\Exceptions\Order\OrderException;
use Illuminate\Database\Eloquent\Builder;
use Modules\Order\Http\Resources\Order\OrderApiResource;
use Modules\Order\Services\Order\Pipeline\ApplyCouponStep;
use Modules\Order\Services\Order\Pipeline\CreateOrderStep;
use Modules\Order\Services\Order\Pipeline\UpdateStockStep;
use Modules\Order\Services\Order\Pipeline\RecordPaymentStep;
use Modules\Order\Services\Order\Pipeline\ValidateBuyerStep;
use Modules\Order\Services\Order\Pipeline\ValidateStockStep;
use Modules\Order\Services\Order\Pipeline\ProcessPaymentStep;
use Modules\Order\Services\Order\Pipeline\CalculatePricesStep;
use Modules\Order\Services\Order\Pipeline\CreateOrderItemsStep;
use Modules\Catalog\Exceptions\Product\Variant\OutOfStockException;
use Modules\Order\Repositories\Interface\Order\OrderRepositoryInterface;
use Modules\Order\Contracts\OrderServiceInterface;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ValidateBuyerStep $validateBuyerStep,
        private ValidateStockStep $validateStockStep,
        private CalculatePricesStep $calculatePricesStep,
        private ApplyCouponStep $applyCouponStep,
        private ProcessPaymentStep $processPaymentStep,
        private CreateOrderStep $createOrderStep,
        private CreateOrderItemsStep $createOrderItemsStep,
        private UpdateStockStep $updateStockStep,
        private RecordPaymentStep $recordPaymentStep
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
            $orderResource = DB::transaction(function () use ($data) {
                // Process through pipeline
                $result = app(Pipeline::class)
                    ->send($data)
                    ->through([
                        $this->validateBuyerStep,
                        $this->validateStockStep,
                        $this->calculatePricesStep,
                        $this->applyCouponStep,
                        $this->processPaymentStep,
                        $this->createOrderStep,
                        $this->createOrderItemsStep,
                        $this->updateStockStep,
                        $this->recordPaymentStep,
                    ])
                    ->thenReturn();

                // Return the order resource
                $order = $result['_order'];
                return new OrderApiResource($order);
            }, 5);

            // Dispatch OrderCreated event after successful order creation
            if ($orderResource instanceof OrderApiResource) {
                $order = $orderResource->resource;
                if ($order instanceof Order) {
                    OrderCreated::dispatch($order);
                }
            }

            return $orderResource;
        } catch (OrderException $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        } catch (OutOfStockException $e) {
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

    public function getUserOrders()
    {
        $userId = Auth::id();
        
        return $this->orderRepository->getQueryBuilder()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }



    public function isDelivered(int $orderId): bool
    {
        return $this->orderRepository->isDelivered($orderId);
    }

    public function updateOrder(int $id, array $data): Order
    {
        // If status is being updated, use updateOrderStatus for logging
        if (isset($data['status'])) {
            $status = $data['status'] instanceof OrderStatus
                ? $data['status']->value
                : $data['status'];
            return $this->updateOrderStatus($id, $status);
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

        $oldStatus = $order->status instanceof OrderStatus
            ? $order->status->value
            : $order->status;

        // Update order status via repository
        $updatedOrder = $this->orderRepository->updateStatus($id, $status);

        // Dispatch OrderStatusChanged event
        OrderStatusChanged::dispatch($updatedOrder, $oldStatus, $status);

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
        return $this->orderRepository->markOrdersAsProcessing($ids);
    }

    public function markOrdersAsShipped(array $ids): int
    {
        return $this->orderRepository->markOrdersAsShipped($ids);
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
        $status = $order->status;
        return $status === OrderStatus::CANCELLED || $status === OrderStatus::REFUNDED;
    }

    protected function getStatusTransitions(): array
    {
        return config('order.status_transitions');
    }

    public function canUpdateStatus(Order $order, string $newStatus): bool
    {
        // Check if new status is valid
        if (!in_array($newStatus, OrderStatus::toArray())) {
            return false;
        }

        // Get current status value
        $currentStatus = $order->status instanceof OrderStatus
            ? $order->status->value
            : $order->status;

        // If status hasn't changed, allow it
        if ($currentStatus === $newStatus) {
            return true;
        }

        $transitions = $this->getStatusTransitions();

        if (!isset($transitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, $transitions[$currentStatus]);
    }

    public function getAvailableStatuses(Order $order): array
    {
        $currentStatus = $order->status instanceof OrderStatus
            ? $order->status->value
            : $order->status;

        $transitions = $this->getStatusTransitions();
        $availableStatuses = $transitions[$currentStatus] ?? [];

        // Always include current status (user can keep it the same)
        $availableStatuses[] = $currentStatus;
        $availableStatuses = array_unique($availableStatuses);

        // Build options array with translations using enum
        $options = [];
        foreach ($availableStatuses as $status) {
            $enumStatus = OrderStatus::tryFrom($status);
            $options[$status] = $enumStatus?->label() ?? $status;
        }

        return $options;
    }
}
