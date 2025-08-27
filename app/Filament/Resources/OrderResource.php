<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'orders';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Orders';

    protected static ?string $recordTitleAttribute = 'order_number';

    /**
     * Get the translated navigation group
     */
    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.store_management');
    }

    /**
     * Get the translated navigation label
     */
    public static function getNavigationLabel(): string
    {
        return __('app.resources.order.navigation_label');
    }

    /**
     * Get the translated model label
     */
    public static function getModelLabel(): string
    {
        return __('app.resources.order.label');
    }

    /**
     * Get the translated plural model label
     */
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
                            ->options([
                                Order::PENDING => __('app.forms.order.status_options.pending'),
                                Order::PROCESSING => __('app.forms.order.status_options.processing'),
                                Order::SHIPPED => __('app.forms.order.status_options.shipped'),
                                Order::DELIVERED => __('app.forms.order.status_options.delivered'),
                                Order::CANCELLED => __('app.forms.order.status_options.cancelled'),
                                Order::REFUNDED => __('app.forms.order.status_options.refunded'),
                            ])
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-truck'),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label(__('app.forms.order.admin_notes'))
                            ->placeholder(__('app.forms.order.admin_notes_placeholder'))
                            ->rows(3)
                            ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('user.name')
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
                        Order::PENDING => 'warning',
                        Order::PROCESSING => 'info',
                        Order::SHIPPED => 'primary',
                        Order::DELIVERED => 'success',
                        Order::CANCELLED => 'danger',
                        Order::REFUNDED => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        Order::PENDING => 'heroicon-o-clock',
                        Order::PROCESSING => 'heroicon-o-cog-6-tooth',
                        Order::SHIPPED => 'heroicon-o-truck',
                        Order::DELIVERED => 'heroicon-o-check-circle',
                        Order::CANCELLED => 'heroicon-o-x-circle',
                        Order::REFUNDED => 'heroicon-o-arrow-path',
                        default => 'heroicon-o-question-mark-circle',
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
                    ->tooltip(function (Order $record): ?string {
                        return $record->address?->full_address;
                    })
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
                        Order::PENDING => __('app.status.pending'),
                        Order::PROCESSING => __('app.status.processing'),
                        Order::SHIPPED => __('app.status.shipped'),
                        Order::DELIVERED => __('app.status.delivered'),
                        Order::CANCELLED => __('app.status.cancelled'),
                        Order::REFUNDED => __('app.status.refunded'),
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
                                        ->where('status', Order::DELIVERED);
                                })->orWhereHas('payments', function ($paymentQuery) {
                                    // Credit card payments that are successful
                                    $paymentQuery->where('status', 'paid');
                                });
                            } elseif ($status === 'pending') {
                                $query->where(function ($subQuery) {
                                    // Cash on delivery orders that are not delivered
                                    $subQuery->where('payment_method', 'cash_on_delivery')
                                        ->where('status', '!=', Order::DELIVERED);
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->visible(
                            fn(Order $record): bool =>
                            in_array($record->status, [Order::PENDING, Order::PROCESSING, Order::SHIPPED])
                        ),

                    // Tables\Actions\Action::make('print_invoice')
                    //     ->label(__('app.actions.print_invoice'))
                    //     ->icon('heroicon-o-printer')
                    //     ->color('success')
                    //     ->url(fn(Order $record): string => route('admin.orders.invoice', $record))
                    //     ->openUrlInNewTab(),

                    // Tables\Actions\Action::make('send_notification')
                    //     ->label(__('app.actions.notify_customer'))
                    //     ->icon('heroicon-o-bell')
                    //     ->color('primary')
                    //     ->action(function (Order $record) {
                    //         // Logic to send notification to customer
                    //     })
                    //     ->requiresConfirmation()
                    //     ->modalHeading(__('app.modals.send_notification.heading'))
                    //     ->modalDescription(__('app.modals.send_notification.description')),

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.modals.delete_order.heading'))
                        ->modalDescription(__('app.modals.delete_order.description'))
                        ->visible(
                            fn(Order $record): bool =>
                            in_array($record->status, [Order::CANCELLED, Order::REFUNDED])
                        ),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_processing')
                        ->label(__('app.bulk_actions.mark_processing'))
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => Order::PROCESSING]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_as_shipped')
                        ->label(__('app.bulk_actions.mark_shipped'))
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => Order::SHIPPED]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('export_orders')
                        ->label(__('app.bulk_actions.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Export logic here
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
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
                                                Order::PENDING => 'warning',
                                                Order::PROCESSING => 'info',
                                                Order::SHIPPED => 'primary',
                                                Order::DELIVERED => 'success',
                                                Order::CANCELLED => 'danger',
                                                Order::REFUNDED => 'gray',
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
                                            ->badge()
                                            ->color('info'),

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
                        Infolists\Components\TextEntry::make('user.name')
                            ->label(__('app.fields.customer_name')),

                        Infolists\Components\TextEntry::make('user.email')
                            ->label(__('app.fields.customer_email'))
                            ->copyable(),

                        Infolists\Components\TextEntry::make('user.phone')
                            ->label(__('app.fields.customer_phone'))
                            ->copyable(),

                        Infolists\Components\TextEntry::make('address.full_address')
                            ->label(__('app.fields.shipping_address'))
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Infolists\Components\Section::make(__('app.sections.order_items'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                Infolists\Components\Split::make([
                                    Infolists\Components\ImageEntry::make('product.primary_image')
                                        ->label('')
                                        ->height(60)
                                        ->width(60),

                                    Infolists\Components\Group::make([
                                        Infolists\Components\TextEntry::make('product.name')
                                            ->label(__('app.fields.product_name'))
                                            ->weight('bold'),

                                        Infolists\Components\TextEntry::make('quantity')
                                            ->label(__('app.fields.quantity'))
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('price')
                                            ->label(__('app.fields.unit_price'))
                                            ->money('USD'),

                                        Infolists\Components\TextEntry::make('discount_price')
                                            ->label(__('app.fields.discount'))
                                            ->money('USD')
                                            ->color('danger')
                                            ->visible(fn($state): bool => !empty($state)),
                                    ])->columns(2),

                                    Infolists\Components\TextEntry::make('total_price')
                                        ->label(__('app.fields.total'))
                                        ->money('USD')
                                        ->weight('bold')
                                        ->color('success'),
                                ]),
                            ])
                            ->columns(1),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make(__('app.sections.payment_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label(__('app.fields.payment_method')),

                        Infolists\Components\RepeatableEntry::make('payments')
                            ->label(__('app.fields.payment_transactions'))
                            ->schema([
                                Infolists\Components\TextEntry::make('transaction_id')
                                    ->label(__('app.fields.transaction_id'))
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('amount')
                                    ->label(__('app.fields.amount'))
                                    ->money('USD'),

                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('app.fields.status'))
                                    ->badge(),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('app.fields.processed_at'))
                                    ->dateTime(),
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
            // Add relation managers if needed
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'user',
                'address',
                'items.product.translations',
                'payments'
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Order::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }
}
