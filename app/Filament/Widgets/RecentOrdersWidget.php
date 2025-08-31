<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?string $heading = null;
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('app.widgets.orders.recent_orders_heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['user', 'items.product.translations', 'payments'])
                    ->latest()
                    ->limit(15)
            )
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

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('app.widgets.orders.status'))
                    ->colors([
                        'warning' => Order::PENDING,
                        'info' => Order::PROCESSING,
                        'primary' => Order::SHIPPED,
                        'success' => Order::DELIVERED,
                        'danger' => Order::CANCELLED,
                        'gray' => Order::REFUNDED,
                    ])
                    ->icons([
                        'heroicon-m-clock' => Order::PENDING,
                        'heroicon-m-cog-6-tooth' => Order::PROCESSING,
                        'heroicon-m-truck' => Order::SHIPPED,
                        'heroicon-m-check-circle' => Order::DELIVERED,
                        'heroicon-m-x-circle' => Order::CANCELLED,
                        'heroicon-m-arrow-path' => Order::REFUNDED,
                    ])
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

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label(__('app.widgets.orders.payment'))
                    ->state(function (Order $record) {
                        if ($record->payment_method === 'cash_on_delivery') {
                            return $record->status === Order::DELIVERED ? 'paid' : 'pending';
                        }

                        $payment = $record->payments()->latest()->first();
                        return $payment?->status ?? 'unknown';
                    })
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger' => 'failed',
                        'gray' => 'unknown',
                    ])
                    ->icons([
                        'heroicon-m-check-circle' => 'paid',
                        'heroicon-m-clock' => 'pending',
                        'heroicon-m-x-circle' => 'failed',
                        'heroicon-m-question-mark-circle' => 'unknown',
                    ])
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
                                ->options([
                                    Order::PENDING => __('app.status.pending'),
                                    Order::PROCESSING => __('app.status.processing'),
                                    Order::SHIPPED => __('app.status.shipped'),
                                    Order::DELIVERED => __('app.status.delivered'),
                                    Order::CANCELLED => __('app.status.cancelled'),
                                ])
                                ->required()
                                ->native(false)
                                ->default(fn(Order $record) => $record->status),
                        ])
                        ->action(function (array $data, Order $record) {
                            $record->update(['status' => $data['status']]);

                            \Filament\Notifications\Notification::make()
                                ->title(__('app.widgets.orders.status_updated'))
                                ->body(__('app.widgets.orders.order_status_updated', [
                                    'order' => $record->order_number,
                                    'status' => __('app.status.' . $data['status'])
                                ]))
                                ->success()
                                ->send();
                        })
                        ->visible(fn(Order $record): bool => $record->canBeEdited()),
                ])
            ])
            ->paginated([10, 15, 20])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Auto-refresh every 30 seconds
            ->striped();
    }

    private function getStockQuery(): Builder
    {
        return match ($this->filter) {
            'out_of_stock' => Product::where('quantity', 0)
                ->with(['translations', 'variants', 'colors']),

            'low_stock' => Product::where('quantity', '>', 0)
                ->where('quantity', '<=', 10)
                ->with(['translations', 'variants', 'colors']),

            'critical_stock' => Product::where('quantity', '>', 0)
                ->where('quantity', '<=', 5)
                ->with(['translations', 'variants', 'colors']),

            default => Product::where('quantity', '<=', 10)
                ->with(['translations', 'variants', 'colors']),
        };
    }
}
