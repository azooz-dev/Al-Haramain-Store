<?php

namespace App\Services\Order\Pipeline;

use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Exceptions\Order\OrderException;

class CreateOrderStep implements OrderProcessingStep
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function handle(array $data, \Closure $next)
    {
        $order = $this->orderRepository->store($data);
        
        if (!$order) {
            throw new OrderException(__('app.messages.order.order_error'), 500);
        }

        $data['_order'] = $order;

        return $next($data);
    }
}


