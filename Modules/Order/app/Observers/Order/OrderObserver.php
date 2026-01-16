<?php

namespace Modules\Order\Observers\Order;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Events\OrderCreated;

class OrderObserver
{

    /**
     * Handle the order "created" event.
     */
    public function created(Order $order): void
    {
        // Dispatch OrderCreated event
        // Admin module will listen and send notifications
        // Analytics module will listen and invalidate cache
        OrderCreated::dispatch($order);
    }

    /**
     * Handle the order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Dispatch OrderStatusChanged event when status changes
        // Analytics module will listen and invalidate cache
        if ($order->isDirty('status')) {
            $originalStatus = $order->getOriginal('status');
            // Convert string to enum if necessary
            $oldStatus = $originalStatus instanceof \Modules\Order\Enums\OrderStatus
                ? $originalStatus
                : \Modules\Order\Enums\OrderStatus::from($originalStatus);
            
            \Modules\Order\Events\OrderStatusChanged::dispatch(
                $order,
                $oldStatus,
                $order->status
            );
        }
    }
}
