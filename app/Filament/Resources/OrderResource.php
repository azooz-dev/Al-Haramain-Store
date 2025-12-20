<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Order\Entities\Order\Order;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Modules\Order\Contracts\OrderServiceInterface;
use Modules\Order\Enums\OrderStatus;
use App\Filament\Concerns\SendsFilamentNotifications;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\PaymentsRelationManager;

class OrderResource extends Resource
{
    use SendsFilamentNotifications;
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'orders';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Orders';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.store_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.resources.order.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('app.resources.order.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.resources.order.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.forms.order.status_management'))
                    ->description(__('app.forms.order.status_management_description'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('app.forms.order.status'))
                            ->options(function (?Order $record) {
                                if (!$record) {
                                    // For new orders, show all statuses (though orders are typically created as PENDING)
                                    return [
                                        OrderStatus::PENDING->value => __('app.status.pending'),
                                        OrderStatus::PROCESSING->value => __('app.status.processing'),
                                        OrderStatus::SHIPPED->value => __('app.status.shipped'),
                                        OrderStatus::DELIVERED->value => __('app.status.delivered'),
                                        OrderStatus::CANCELLED->value => __('app.status.cancelled'),
                                        OrderStatus::REFUNDED->value => __('app.status.refunded'),
                                    ];
                                }

                                // For existing orders, show only available status transitions
                                $orderService = app(OrderServiceInterface::class);
                                return $orderService->getAvailableStatuses($record);
                            })
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-truck')
                            ->helperText(function (?Order $record) {
                                if (!$record) {
                                    return null;
                                }

                                $orderService = app(OrderServiceInterface::class);
                                $availableStatuses = $orderService->getAvailableStatuses($record);

                                if (count($availableStatuses) === 1) {
                                    return __('app.forms.order.status_terminal_state');
                                }

                                return __('app.forms.order.status_available_transitions');
                            }),

                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('app.columns.order.order_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-hashtag')
                    ->prefix('#'),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label(__('app.columns.order.customer'))
                    ->searchable(['users.name', 'users.email'])
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-o-user')
                    ->wrap()
                    ->description(fn(Order $record): string => $record->user->email ?? ''),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('app.columns.order.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        OrderStatus::PENDING->value => 'warning',
                        OrderStatus::PROCESSING->value => 'info',
                        OrderStatus::SHIPPED->value => 'primary',
                        OrderStatus::DELIVERED->value => 'success',
                        OrderStatus::CANCELLED->value => 'danger',
                        OrderStatus::REFUNDED->value => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        OrderStatus::PENDING->value => 'heroicon-o-clock',
                        OrderStatus::PROCESSING->value => 'heroicon-o-cog-6-tooth',
                        OrderStatus::SHIPPED->value => 'heroicon-o-truck',
                        OrderStatus::DELIVERED->value => 'heroicon-o-check-circle',
                        OrderStatus::CANCELLED->value => 'heroicon-o-x-circle',
                        OrderStatus::REFUNDED->value => 'heroicon-o-arrow-path',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        OrderStatus::PENDING->value => __('app.status.pending'),
                        OrderStatus::PROCESSING->value => __('app.status.processing'),
                        OrderStatus::SHIPPED->value => __('app.status.shipped'),
                        OrderStatus::DELIVERED->value => __('app.status.delivered'),
                        OrderStatus::CANCELLED->value => __('app.status.cancelled'),
                        OrderStatus::REFUNDED->value => __('app.status.refunded'),
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label(__('app.columns.order.items_count'))
                    ->counts('items')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->icon('heroicon-o-shopping-cart')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('app.columns.order.total_amount'))
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('app.columns.order.payment_method'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'credit_card' => 'success',
                        'paypal' => 'info',
                        'cash_on_delivery' => 'warning',
                        'bank_transfer' => 'primary',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'credit_card' => 'heroicon-o-credit-card',
                        'paypal' => 'heroicon-o-globe-alt',
                        'cash_on_delivery' => 'heroicon-o-banknotes',
                        'bank_transfer' => 'heroicon-o-building-library',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'credit_card' => __('app.payment.credit_card'),
                        'paypal' => __('app.payment.paypal'),
                        'cash_on_delivery' => __('app.payment.cash_on_delivery'),
                        'bank_transfer' => __('app.payment.bank_transfer'),
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('app.columns.order.payment_status'))
                    ->badge()
                    ->state(function (Order $record) {
                        // Use model accessor
                        return $record->payment_status;
                    })
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger' => 'failed',
                        'gray' => 'unknown',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-x-circle' => 'failed',
                        'heroicon-o-question-mark-circle' => 'unknown',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'paid' => __('app.payment_status.paid'),
                        'pending' => __('app.payment_status.pending'),
                        'failed' => __('app.payment_status.failed'),
                        'unknown' => __('app.payment_status.unknown'),
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('address.full_address')
                    ->label(__('app.columns.order.shipping_address'))
                    ->limit(30)
                    ->tooltip(fn(Order $record): ?string => $record->address?->getFullAddressAttribute())
                    ->icon('heroicon-o-map-pin')
                    ->color('gray')
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.columns.order.created_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app.columns.order.updated_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('app.filters.order_status'))
                    ->options([
                        OrderStatus::PENDING->value => __('app.status.pending'),
                        OrderStatus::PROCESSING->value => __('app.status.processing'),
                        OrderStatus::SHIPPED->value => __('app.status.shipped'),
                        OrderStatus::DELIVERED->value => __('app.status.delivered'),
                        OrderStatus::CANCELLED->value => __('app.status.cancelled'),
                        OrderStatus::REFUNDED->value => __('app.status.refunded'),
                    ])
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(__('app.filters.payment_method'))
                    ->options([
                        'credit_card' => __('app.payment.credit_card'),
                        'paypal' => __('app.payment.paypal'),
                        'cash_on_delivery' => __('app.payment.cash_on_delivery'),
                        'bank_transfer' => __('app.payment.bank_transfer'),
                    ])
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('total_amount')
                    ->label(__('app.filters.order_amount'))
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount_from')
                                    ->label(__('app.filters.amount_from'))
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('amount_to')
                                    ->label(__('app.filters.amount_to'))
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn(Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn(Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->label(__('app.filters.order_date'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('app.filters.created_from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('app.filters.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('payment_status')
                    ->label(__('app.filters.payment_status'))
                    ->form([
                        Forms\Components\Select::make('payment_status')
                            ->label(__('app.filters.payment_status'))
                            ->options([
                                'paid' => __('app.payment_status.paid'),
                                'pending' => __('app.payment_status.pending'),
                                'failed' => __('app.payment_status.failed'),
                            ])
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['payment_status'])) {
                            return $query;
                        }

                        $status = $data['payment_status'];

                        return $query->where(function ($query) use ($status) {
                            if ($status === 'paid') {
                                $query->where(function ($subQuery) {
                                    // Cash on delivery orders that are delivered
                                    $subQuery->where('payment_method', 'cash_on_delivery')
                                        ->where('status', OrderStatus::DELIVERED->value);
                                })->orWhereHas('payments', function ($paymentQuery) {
                                    // Credit card payments that are successful
                                    $paymentQuery->where('status', 'paid');
                                });
                            } elseif ($status === 'pending') {
                                $query->where(function ($subQuery) {
                                    // Cash on delivery orders that are not delivered
                                    $subQuery->where('payment_method', 'cash_on_delivery')
                                        ->where('status', '!=', OrderStatus::DELIVERED->value);
                                })->orWhereHas('payments', function ($paymentQuery) {
                                    // Credit card payments that are pending
                                    $paymentQuery->where('status', 'pending');
                                });
                            } elseif ($status === 'failed') {
                                $query->whereHas('payments', function ($paymentQuery) {
                                    $paymentQuery->where('status', 'failed');
                                });
                            }
                        });
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_processing')
                        ->label(__('app.bulk_actions.mark_processing'))
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info')
                        ->action(function ($records) {
                            $orderService = app(OrderServiceInterface::class);
                            $ids = $records->pluck('id')->toArray();
                            $orderService->markOrdersAsProcessing($ids);
                        })
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.order.confirm_change_status_heading'))
                        ->modalDescription(__('app.messages.order.confirm_change_status_description'))
                        ->modalSubmitActionLabel(__('app.actions.change_status'))
                        ->successNotification(fn($records) => self::buildSuccessNotification(
                            __('app.messages.order.status_changed_success'),
                            __('app.messages.order.status_changed_success_body', ['status' => OrderStatus::PROCESSING->value])
                        )),

                    Tables\Actions\BulkAction::make('mark_as_shipped')
                        ->label(__('app.bulk_actions.mark_shipped'))
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->action(function ($records) {
                            $orderService = app(OrderServiceInterface::class);
                            $ids = $records->pluck('id')->toArray();
                            $orderService->markOrdersAsShipped($ids);
                        })
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.order.confirm_change_status_heading'))
                        ->modalDescription(__('app.messages.order.confirm_change_status_description'))
                        ->modalSubmitActionLabel(__('app.actions.change_status'))
                        ->successNotification(fn($records) => self::buildSuccessNotification(
                            __('app.messages.order.status_changed_success'),
                            __('app.messages.order.status_changed_success_body', ['status' => OrderStatus::SHIPPED->value])
                        )),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.order.confirm_delete_heading'))
                        ->modalDescription(__('app.messages.order.confirm_delete_description'))
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->successNotification(fn($records) => self::buildSuccessNotification(
                            __('app.messages.order.deleted_success'),
                            __('app.messages.order.deleted_success_body', ['name' => $records->name])
                        )),
                ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->visible(fn(Order $record): bool => in_array($record->status, [OrderStatus::PENDING->value, OrderStatus::PROCESSING->value, OrderStatus::SHIPPED->value])),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.order.delete_order.heading'))
                        ->modalDescription(__('app.messages.order.delete_order.description'))
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->modalCancelActionLabel(__('app.actions.cancel'))
                        ->before(function (Order $record) {
                            $orderService = app(OrderServiceInterface::class);
                            if (!$orderService->canDeleteOrder($record)) {
                                throw new \Filament\Support\Exceptions\Halt();
                            }
                            return true;
                        })
                        ->successNotification(fn($record) => self::buildSuccessNotification(
                            __('app.messages.order.deleted_success'),
                            __('app.messages.order.deleted_success_body', ['name' => $record->order_number])
                        ))
                        ->visible(fn(Order $record): bool => in_array($record->status, [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])),

                    Tables\Actions\Action::make('download_invoice')
                        ->label(__('app.actions.generate_invoice'))
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->action(function (Order $record) {
                            return response()->streamDownload(function () use ($record) {
                                echo view('invoices.order', ['order' => $record])->render();
                            }, 'invoice-' . $record->order_number . '.html');
                        })
                        ->successNotification(fn($record) => self::buildSuccessNotification(
                            __('app.messages.order.invoice_generated_success'),
                            __('app.messages.order.invoice_generated_success_body', ['name' => $record->name])
                        )),
                ])
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->extremePaginationLinks()
            ->deferLoading();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        // Note: The View page will define a richer layout; this is kept for default behavior.
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('app.sections.order_overview'))
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\Group::make([
                                        Infolists\Components\TextEntry::make('order_number')
                                            ->label(__('app.fields.order_number'))
                                            ->badge()
                                            ->color('primary')
                                            ->prefix('#'),

                                        Infolists\Components\TextEntry::make('status')
                                            ->label(__('app.fields.status'))
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                OrderStatus::PENDING->value => 'warning',
                                                OrderStatus::PROCESSING->value => 'info',
                                                OrderStatus::SHIPPED->value => 'primary',
                                                OrderStatus::DELIVERED->value => 'success',
                                                OrderStatus::CANCELLED->value => 'danger',
                                                OrderStatus::REFUNDED->value => 'gray',
                                                default => 'gray',
                                            }),

                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label(__('app.fields.order_date'))
                                            ->dateTime('F j, Y g:i A'),
                                    ]),

                                    Infolists\Components\Group::make([
                                        Infolists\Components\TextEntry::make('total_amount')
                                            ->label(__('app.fields.total_amount'))
                                            ->money('USD')
                                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                            ->weight('bold')
                                            ->color('success'),

                                        Infolists\Components\TextEntry::make('payment_method')
                                            ->label(__('app.fields.payment_method'))
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('items_count')
                                            ->label(__('app.fields.total_items'))
                                            ->state(fn(Order $record): int => $record->items->sum('quantity')),
                                    ]),
                                ]),
                        ]),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('app.sections.customer_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user.full_name')->label(__('app.fields.customer_name')),
                        Infolists\Components\TextEntry::make('user.email')->label(__('app.fields.customer_email'))->copyable(),
                        Infolists\Components\TextEntry::make('user.phone')->label(__('app.fields.customer_phone'))->copyable(),
                        Infolists\Components\TextEntry::make('address.full_address')->label(__('app.fields.shipping_address'))->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Infolists\Components\Section::make(__('app.sections.payment_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label(__('app.fields.payment_method'))
                            ->formatStateUsing(function ($state) {
                                // Use translation keys for payment methods
                                return match ($state) {
                                    'cash_on_delivery' => __('app.payment.cash_on_delivery'),
                                    'credit_card' => __('app.payment.credit_card'),
                                    'paypal' => __('app.payment.paypal'),
                                    'bank_transfer' => __('app.payment.bank_transfer'),
                                    default => $state,
                                };
                            }),
                        Infolists\Components\RepeatableEntry::make('payments')
                            ->label(__('app.fields.payment_transactions'))
                            ->schema([
                                Infolists\Components\TextEntry::make('transaction_id')->label(__('app.fields.transaction_id'))->copyable(),
                                Infolists\Components\TextEntry::make('amount')->label(__('app.fields.amount'))->money('USD'),
                                Infolists\Components\TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                                Infolists\Components\TextEntry::make('paid_at')->label(__('app.fields.processed_at'))->dateTime(),
                            ])
                            ->columns(4)
                            ->visible(fn(Order $record): bool => $record->payments->isNotEmpty()),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    /**
     * Get the Eloquent query for the resource
     * Uses service layer for query building
     */
    public static function getEloquentQuery(): Builder
    {
        return app(OrderServiceInterface::class)->getQueryBuilder();
    }

    /**
     * Get navigation badge with pending orders count
     */
    public static function getNavigationBadge(): ?string
    {
        return app(OrderServiceInterface::class)->getOrdersCountByStatus(OrderStatus::PENDING->value) ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }
}
