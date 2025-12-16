<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\Order\Repositories\Interface\OrderItem\OrderItemRepositoryInterface;
use Modules\Order\Exceptions\Order\OrderException;

class CreateOrderItemsStep implements OrderProcessingStep
{
  public function __construct(
    private OrderItemRepositoryInterface $orderItemRepository
  ) {}

  public function handle(array $data, \Closure $next)
  {
    $order = $data['_order'] ?? null;

    if (!$order) {
      throw new OrderException(__('app.messages.order.order_error'), 500);
    }

    $created = $this->orderItemRepository->createMany($data['items'], $order->id);

    if (!$created) {
      throw new OrderException(__('app.messages.order.order_error'), 500);
    }

    return $next($data);
  }
}
