<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Services\Order\OrderService;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function __construct(
        protected OrderService $orderService
    ) {
        parent::__construct();
    }

    public function getTabs(): array
    {

        return [
            'all' => Tab::make(__('app.tabs.all_orders'))
                ->icon('heroicon-o-queue-list')
                ->badge($this->orderService->getOrdersCount()),

            'pending' => Tab::make(__('app.tabs.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::PENDING))
                ->badge($this->orderService->getOrdersCountByStatus(Order::PENDING))
                ->badgeColor('warning'),

            'processing' => Tab::make(__('app.tabs.processing'))
                ->icon('heroicon-o-cog-6-tooth')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::PROCESSING))
                ->badge($this->orderService->getOrdersCountByStatus(Order::PROCESSING))
                ->badgeColor('info'),

            'shipped' => Tab::make(__('app.tabs.shipped'))
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::SHIPPED))
                ->badge($this->orderService->getOrdersCountByStatus(Order::SHIPPED))
                ->badgeColor('primary'),

            'delivered' => Tab::make(__('app.tabs.delivered'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::DELIVERED))
                ->badge($this->orderService->getOrdersCountByStatus(Order::DELIVERED))
                ->badgeColor('success'),

            'cancelled' => Tab::make(__('app.tabs.cancelled'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::CANCELLED))
                ->badge($this->orderService->getOrdersCountByStatus(Order::CANCELLED))
                ->badgeColor('danger'),

            'refunded' => Tab::make(__('app.tabs.refunded'))
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::REFUNDED))
                ->badge($this->orderService->getOrdersCountByStatus(Order::REFUNDED))
                ->badgeColor('gray'),
        ];
    }
}
