<?php

namespace Modules\Analytics\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Modules\Order\Events\OrderCreated::class => [
            \Modules\Analytics\Listeners\InvalidateDashboardCache::class,
        ],
        \Modules\Order\Events\OrderStatusChanged::class => [
            \Modules\Analytics\Listeners\InvalidateCacheOnOrderChange::class,
        ],
        \Modules\User\Events\UserCreated::class => [
            \Modules\Analytics\Listeners\InvalidateDashboardCache::class,
        ],
        \Modules\Catalog\Events\ProductUpdated::class => [
            \Modules\Analytics\Listeners\InvalidateDashboardCache::class,
        ],
        \Modules\Review\Events\ReviewCreated::class => [
            \Modules\Analytics\Listeners\InvalidateDashboardCache::class,
        ],
        \Modules\Review\Events\ReviewUpdated::class => [
            \Modules\Analytics\Listeners\InvalidateDashboardCache::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
