<?php

namespace Modules\Order\Observers\Order;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Events\OrderCreated;
use Modules\Analytics\Services\DashboardCacheHelper;
use App\Services\Cache\CacheService;

class OrderObserver
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Handle the order "created" event.
     */
    public function created(Order $order): void
    {
        // Dispatch OrderCreated event - Admin module will listen and send notifications
        OrderCreated::dispatch($order);

        // Invalidate dashboard widget cache
        DashboardCacheHelper::flushAll();
        
        // Invalidate analytics cache
        $this->cacheService->flush(['dashboard', 'revenue', 'orders']);
    }

    /**
     * Handle the order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Invalidate cache when order status or total amount changes
        if ($order->isDirty(['status', 'total_amount'])) {
            DashboardCacheHelper::flushAll();
            $this->cacheService->flush(['dashboard', 'revenue', 'orders']);
        }
    }
}
