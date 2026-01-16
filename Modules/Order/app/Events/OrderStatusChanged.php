<?php

namespace Modules\Order\Events;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Order $order,
        public OrderStatus $oldStatus,
        public OrderStatus $newStatus
    ) {}
}

