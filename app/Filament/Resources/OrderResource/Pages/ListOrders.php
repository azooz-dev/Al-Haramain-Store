<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Contracts\OrderServiceInterface;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function __construct(
        protected OrderServiceInterface $orderService
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
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::PENDING))
                ->badge($this->orderService->getOrdersCountByStatus(OrderStatus::PENDING->value))
                ->badgeColor('warning'),

            'processing' => Tab::make(__('app.tabs.processing'))
                ->icon('heroicon-o-cog-6-tooth')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::PROCESSING))
                ->badge($this->orderService->getOrdersCountByStatus(OrderStatus::PROCESSING->value))
                ->badgeColor('info'),

            'shipped' => Tab::make(__('app.tabs.shipped'))
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::SHIPPED))
                ->badge($this->orderService->getOrdersCountByStatus(OrderStatus::SHIPPED->value))
                ->badgeColor('primary'),

            'delivered' => Tab::make(__('app.tabs.delivered'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::DELIVERED))
                ->badge($this->orderService->getOrdersCountByStatus(OrderStatus::DELIVERED->value))
                ->badgeColor('success'),

            'cancelled' => Tab::make(__('app.tabs.cancelled'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::CANCELLED))
                ->badge($this->orderService->getOrdersCountByStatus(OrderStatus::CANCELLED->value))
                ->badgeColor('danger'),

            'refunded' => Tab::make(__('app.tabs.refunded'))
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::REFUNDED))
                ->badge($this->orderService->getOrdersCountByStatus(OrderStatus::REFUNDED->value))
                ->badgeColor('gray'),
        ];
    }
}
