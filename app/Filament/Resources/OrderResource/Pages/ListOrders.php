<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\Action::make('export_orders')
    //             ->label(__('app.actions.export_orders'))
    //             ->icon('heroicon-o-arrow-down-tray')
    //             ->color('success')
    //             ->action(function () {
    //                 // Export logic here
    //             }),

    //         Actions\Action::make('order_analytics')
    //             ->label(__('app.actions.view_analytics'))
    //             ->icon('heroicon-o-chart-bar')
    //             ->color('primary')
    //             // ->url(route('admin.orders.analytics'))
    //             ->openUrlInNewTab(),
    //     ];
    // }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('app.tabs.all_orders'))
                ->icon('heroicon-o-queue-list')
                ->badge(Order::count()),

            'pending' => Tab::make(__('app.tabs.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::PENDING))
                ->badge(Order::where('status', Order::PENDING)->count())
                ->badgeColor('warning'),

            'processing' => Tab::make(__('app.tabs.processing'))
                ->icon('heroicon-o-cog-6-tooth')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::PROCESSING))
                ->badge(Order::where('status', Order::PROCESSING)->count())
                ->badgeColor('info'),

            'shipped' => Tab::make(__('app.tabs.shipped'))
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::SHIPPED))
                ->badge(Order::where('status', Order::SHIPPED)->count())
                ->badgeColor('primary'),

            'delivered' => Tab::make(__('app.tabs.delivered'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::DELIVERED))
                ->badge(Order::where('status', Order::DELIVERED)->count())
                ->badgeColor('success'),

            'cancelled' => Tab::make(__('app.tabs.cancelled'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::CANCELLED))
                ->badge(Order::where('status', Order::CANCELLED)->count())
                ->badgeColor('danger'),

            'refunded' => Tab::make(__('app.tabs.refunded'))
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Order::REFUNDED))
                ->badge(Order::where('status', Order::REFUNDED)->count())
                ->badgeColor('gray'),
        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         OrderResource\Widgets\OrderStatsWidget::class,
    //     ];
    // }
}
