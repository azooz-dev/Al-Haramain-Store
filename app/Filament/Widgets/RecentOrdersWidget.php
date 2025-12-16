<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Services\Order\OrderService;
use App\Filament\Concerns\ResolvesServices;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Services\Dashboard\OrderAnalyticsService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class RecentOrdersWidget extends BaseWidget
{
    use HasWidgetShield, ResolvesServices;

    protected static ?string $heading = null;
    protected static ?int $sort = 6;
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('app.widgets.orders.recent_orders_heading');
    }

    public function table(Table $table): Table
    {
        $orderAnalyticsService = $this->resolveService(OrderAnalyticsService::class);
        $orderService = $this->resolveService(OrderService::class);

        // Get recent orders from service (already has proper eager loading with morphWith)
        $recentOrders = $orderAnalyticsService->getRecentOrders(15);
        $orderIds = $recentOrders->pluck('id')->toArray();

        return $table
            ->query(function () use ($orderIds) {
                $query = Order::query()
                    ->with([
                        'user',
                        'items.orderable' => function ($morphTo) {
                            $morphTo->morphWith([
                                \Modules\Catalog\Entities\Product\Product::class => ['translations'],
                                \App\Models\Offer\Offer::class => ['translations'],
                            ]);
                        },
                        'items.variant',
                        'items.color',
                        'payments',
                    ]);

                if (!empty($orderIds)) {
                    $query->whereIn('id', $orderIds)
                        ->orderByRaw('FIELD(id, ' . implode(',', $orderIds) . ')');
                } else {
                    // Return empty query if no orders
                    $query->whereRaw('1 = 0');
                }

                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('app.widgets.orders.order_number'))
                    ->badge()
                    ->color('primary')
                    ->prefix('#')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.first_name')
                    ->label(__('app.widgets.orders.customer'))
                    ->formatStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->first_name . ' ' . $record->user->last_name;
                        }
                        return 'N/A';
                    })
                    ->description(fn(Order $record): string => $record->user->email ?? '')
                    ->searchable(['users.first_name', 'users.last_name', 'users.email'])
                    ->sortable()
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('app.widgets.orders.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Order::PENDING => 'warning',
                        Order::PROCESSING => 'info',
                        Order::SHIPPED => 'primary',
                        Order::DELIVERED => 'success',
                        Order::CANCELLED => 'danger',
                        Order::REFUNDED => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        Order::PENDING => 'heroicon-m-clock',
                        Order::PROCESSING => 'heroicon-m-cog-6-tooth',
                        Order::SHIPPED => 'heroicon-m-truck',
                        Order::DELIVERED => 'heroicon-m-check-circle',
                        Order::CANCELLED => 'heroicon-m-x-circle',
                        Order::REFUNDED => 'heroicon-m-arrow-path',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        Order::PENDING => __('app.status.pending'),
                        Order::PROCESSING => __('app.status.processing'),
                        Order::SHIPPED => __('app.status.shipped'),
                        Order::DELIVERED => __('app.status.delivered'),
                        Order::CANCELLED => __('app.status.cancelled'),
                        Order::REFUNDED => __('app.status.refunded'),
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label(__('app.widgets.orders.items'))
                    ->state(fn(Order $record) => $record->items->sum('quantity'))
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('app.widgets.orders.amount'))
                    ->money('USD')
                    ->weight('bold')
                    ->color('success')
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('app.widgets.orders.payment'))
                    ->badge()
                    ->state(function (Order $record) {
                        if ($record->payment_method === 'cash_on_delivery') {
                            return $record->status === Order::DELIVERED ? 'paid' : 'pending';
                        }

                        $payment = $record->payments()->latest()->first();
                        return $payment?->status ?? 'unknown';
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'unknown' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'paid' => 'heroicon-m-check-circle',
                        'pending' => 'heroicon-m-clock',
                        'failed' => 'heroicon-m-x-circle',
                        'unknown' => 'heroicon-m-question-mark-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'paid' => __('app.payment_status.paid'),
                        'pending' => __('app.payment_status.pending'),
                        'failed' => __('app.payment_status.failed'),
                        'unknown' => __('app.payment_status.unknown'),
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.widgets.orders.created'))
                    ->since()
                    ->color('gray')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label(__('app.widgets.orders.actions.view'))
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(fn(Order $record): string => route('filament.admin.resources.orders.view', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('edit')
                        ->label(__('app.widgets.orders.actions.edit'))
                        ->icon('heroicon-m-pencil')
                        ->color('warning')
                        ->url(fn(Order $record): string => route('filament.admin.resources.orders.edit', $record))
                        ->openUrlInNewTab()
                        ->visible(fn(Order $record): bool => $record->canBeEdited()),

                    Tables\Actions\Action::make('quick_status_update')
                        ->label(__('app.widgets.orders.actions.update_status'))
                        ->icon('heroicon-m-arrow-path')
                        ->color('primary')
                        ->form([
                            \Filament\Forms\Components\Select::make('status')
                                ->label(__('app.widgets.orders.new_status'))
                                ->options(function (Order $record) use ($orderService) {
                                    return collect($orderService->getAvailableStatuses($record))
                                        ->mapWithKeys(fn($status) => [$status => __('app.status.' . $status)])
                                        ->toArray();
                                })
                                ->required()
                                ->native(false)
                                ->default(fn(Order $record) => $record->status),
                        ])
                        ->action(function (array $data, Order $record) use ($orderService) {
                            $orderService->updateOrderStatus($record->id, $data['status']);

                            \Filament\Notifications\Notification::make()
                                ->title(__('app.widgets.orders.status_updated'))
                                ->body(__('app.widgets.orders.order_status_updated', [
                                    'order' => $record->order_number,
                                    'status' => __('app.status.' . $data['status'])
                                ]))
                                ->success()
                                ->send();
                        })
                        ->visible(function (Order $record) use ($orderService): bool {
                            return $orderService->canUpdateStatus($record, $record->status);
                        }),
                ])
            ])
            ->paginated([10, 15, 20])
            ->defaultSort('created_at', 'desc')
            ->poll('60s') // Auto-refresh every 60 seconds (reduced from 30s)
            ->striped();
    }
}
