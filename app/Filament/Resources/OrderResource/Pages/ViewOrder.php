<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order\Order;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Colors\Color;
use Symfony\Component\Console\Helper\TableSeparator;

class ViewOrder extends ViewRecord
{
  protected static string $resource = OrderResource::class;

  protected function getHeaderActions(): array
  {
    return [
      // Actions\Action::make('print_invoice')
      //   ->label(__('app.actions.print_invoice'))
      //   ->icon('heroicon-o-printer')
      //   ->color('success')
      //   ->url(fn(): string => route('admin.orders.invoice', $this->record))
      //   ->openUrlInNewTab(),

      // Actions\Action::make('send_notification')
      //   ->label(__('app.actions.notify_customer'))
      //   ->icon('heroicon-o-bell')
      //   ->color('primary')
      //   ->action(function () {
      //     // Logic to send notification
      //   })
      //   ->requiresConfirmation()
      //   ->modalHeading(__('app.modals.send_notification.heading'))
      //   ->modalDescription(__('app.modals.send_notification.description')),

      Actions\EditAction::make()
        ->icon('heroicon-o-pencil')
        ->label(__('app.actions.edit'))
        ->visible(fn(): bool => $this->record->canBeEdited()),

      Actions\Action::make('cancel_order')
        ->label(__('app.actions.cancel_order'))
        ->icon('heroicon-o-x-mark')
        ->color('danger')
        ->action(function () {
          $this->record->update(['status' => Order::CANCELLED]);
          $this->refreshFormData(['status']);
        })
        ->requiresConfirmation()
        ->modalHeading(__('app.modals.cancel_order.heading'))
        ->modalDescription(__('app.modals.cancel_order.description'))
        ->visible(fn(): bool => $this->record->canBeCancelled()),

      Actions\Action::make('refund_order')
        ->label(__('app.actions.refund_order'))
        ->icon('heroicon-o-arrow-path')
        ->color('warning')
        ->action(function () {
          $this->record->update(['status' => Order::REFUNDED]);
          $this->refreshFormData(['status']);
        })
        ->requiresConfirmation()
        ->modalHeading(__('app.modals.refund_order.heading'))
        ->modalDescription(__('app.modals.refund_order.description'))
        ->visible(fn(): bool => $this->record->canBeRefunded()),
    ];
  }

  public function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        // Order Overview Section
        Infolists\Components\Section::make(__('app.sections.order_overview'))
          ->icon('heroicon-o-shopping-bag')
          ->description(__('app.sections.order_overview_description'))
          ->schema([
            Infolists\Components\Split::make([
              // Left column - Order Details
              Infolists\Components\Group::make([
                Infolists\Components\TextEntry::make('order_number')
                  ->label(__('app.fields.order_number'))
                  ->badge()
                  ->color('primary')
                  ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                  ->weight('bold')
                  ->prefix('#')
                  ->copyable(),

                Infolists\Components\TextEntry::make('status')
                  ->label(__('app.fields.status'))
                  ->badge()
                  ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
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
                  }),

                Infolists\Components\TextEntry::make('created_at')
                  ->label(__('app.fields.order_date'))
                  ->dateTime('F j, Y \a\t g:i A')
                  ->icon('heroicon-o-calendar')
                  ->color('gray'),

                Infolists\Components\TextEntry::make('updated_at')
                  ->label(__('app.fields.last_updated'))
                  ->dateTime('F j, Y \a\t g:i A')
                  ->icon('heroicon-o-clock')
                  ->color('gray')
                  ->since(),
              ])->grow(false),

              // Right column - Financial Summary
              Infolists\Components\Group::make([
                Infolists\Components\TextEntry::make('total_amount')
                  ->label(__('app.fields.total_amount'))
                  ->money('USD')
                  ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                  ->weight('bold')
                  ->color('success')
                  ->icon('heroicon-o-currency-dollar'),

                Infolists\Components\TextEntry::make('payment_method')
                  ->label(__('app.fields.payment_method'))
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

                Infolists\Components\TextEntry::make('payment_status')
                  ->label(__('app.fields.payment_status'))
                  ->badge()
                  ->state(fn(Order $record): string => $record->payment_status)
                  ->color(fn(Order $record): string => $record->payment_status_color)
                  ->icon(fn(string $state): string => match ($state) {
                    'paid' => 'heroicon-o-check-circle',
                    'pending' => 'heroicon-o-clock',
                    'failed' => 'heroicon-o-x-circle',
                    default => 'heroicon-o-question-mark-circle',
                  })
                  ->formatStateUsing(fn(string $state): string => match ($state) {
                    'paid' => __('app.payment_status.paid'),
                    'pending' => __('app.payment_status.pending'),
                    'failed' => __('app.payment_status.failed'),
                    'unknown' => __('app.payment_status.unknown'),
                    default => $state,
                  }),

                Infolists\Components\TextEntry::make('total_items')
                  ->label(__('app.fields.total_items'))
                  ->state(fn(Order $record): int => $record->total_items)
                  ->badge()
                  ->color('info')
                  ->icon('heroicon-o-shopping-cart'),
              ])->grow(false),
            ])->from('md'),
          ])
          ->headerActions([
            Infolists\Components\Actions\Action::make('track_order')
              ->label(__('app.actions.track_order'))
              ->icon('heroicon-o-map')
              ->color('primary')
              ->url('#')
              ->openUrlInNewTab()
              ->visible(
                fn(Order $record): bool =>
                in_array($record->status, [Order::SHIPPED, Order::DELIVERED])
              ),
          ]),

        // Customer Information Section
        Infolists\Components\Section::make(__('app.sections.customer_information'))
          ->icon('heroicon-o-user')
          ->description(__('app.sections.customer_information_description'))
          ->schema([
            Infolists\Components\Split::make([
              Infolists\Components\Group::make([
                Infolists\Components\TextEntry::make('user.name')
                  ->label(__('app.fields.customer_name'))
                  ->weight('bold')
                  ->icon('heroicon-o-user'),

                Infolists\Components\TextEntry::make('user.email')
                  ->label(__('app.fields.customer_email'))
                  ->copyable()
                  ->icon('heroicon-o-envelope'),

                Infolists\Components\TextEntry::make('user.phone')
                  ->label(__('app.fields.customer_phone'))
                  ->copyable()
                  ->icon('heroicon-o-phone')
                  ->placeholder(__('app.placeholders.not_provided')),
              ])->grow(false),

              Infolists\Components\Group::make([
                Infolists\Components\TextEntry::make('user.created_at')
                  ->label(__('app.fields.customer_since'))
                  ->dateTime('F Y')
                  ->icon('heroicon-o-calendar'),

                Infolists\Components\TextEntry::make('user.orders_count')
                  ->label(__('app.fields.total_orders'))
                  ->state(fn(Order $record): int => $record->user->orders()->count())
                  ->badge()
                  ->color('info'),

                Infolists\Components\TextEntry::make('user.total_spent')
                  ->label(__('app.fields.total_spent'))
                  ->state(
                    fn(Order $record): float =>
                    $record->user->orders()
                      ->whereIn('status', [Order::DELIVERED])
                      ->sum('total_amount')
                  )
                  ->money('USD')
                  ->color('success'),
              ])->grow(false),
            ])->from('md'),
          ])
          ->collapsible(),

        // Shipping Address Section
        Infolists\Components\Section::make(__('app.sections.shipping_address'))
          ->icon('heroicon-o-map-pin')
          ->description(__('app.sections.shipping_address_description'))
          ->schema([
            Infolists\Components\Grid::make(2)
              ->schema([
                Infolists\Components\TextEntry::make('address.recipient_name')
                  ->label(__('app.fields.recipient_name'))
                  ->icon('heroicon-o-user'),

                Infolists\Components\TextEntry::make('address.phone')
                  ->label(__('app.fields.recipient_phone'))
                  ->icon('heroicon-o-phone')
                  ->copyable(),

                Infolists\Components\TextEntry::make('address.address_line_1')
                  ->label(__('app.fields.address_line_1'))
                  ->columnSpanFull()
                  ->icon('heroicon-o-home'),

                Infolists\Components\TextEntry::make('address.address_line_2')
                  ->label(__('app.fields.address_line_2'))
                  ->columnSpanFull()
                  ->icon('heroicon-o-building-office')
                  ->placeholder(__('app.placeholders.not_provided')),

                Infolists\Components\TextEntry::make('address.city')
                  ->label(__('app.fields.city'))
                  ->icon('heroicon-o-building-office-2'),

                Infolists\Components\TextEntry::make('address.state')
                  ->label(__('app.fields.state'))
                  ->icon('heroicon-o-map'),

                Infolists\Components\TextEntry::make('address.postal_code')
                  ->label(__('app.fields.postal_code'))
                  ->icon('heroicon-o-hashtag'),

                Infolists\Components\TextEntry::make('address.country')
                  ->label(__('app.fields.country'))
                  ->icon('heroicon-o-globe-alt'),
              ]),
          ])
          ->collapsible(),

        // Order Items Section
        Infolists\Components\Section::make(__('app.sections.order_items'))
          ->icon('heroicon-o-shopping-cart')
          ->description(
            fn(Order $record): string =>
            __('app.sections.order_items_description', [
              'count' => $record->items->count(),
              'total' => $record->total_items
            ])
          )
          ->schema([
            Infolists\Components\RepeatableEntry::make('items')
              ->label('')
              ->schema([
                Infolists\Components\Split::make([
                  // Product Image
                  Infolists\Components\Group::make([
                    Infolists\Components\ImageEntry::make('product.primary_image_url')
                      ->label('')
                      ->height(80)
                      ->width(80)
                      ->defaultImageUrl('/images/placeholder-product.jpg')
                      ->extraAttributes(['class' => 'rounded-lg border']),
                  ])->grow(false),

                  // Product Details
                  Infolists\Components\Group::make([
                    Infolists\Components\TextEntry::make('product.name')
                      ->label('')
                      ->weight('bold')
                      ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                      ->state(function ($record) {
                        $product = $record->product;
                        return $product->translations
                          ->where('local', app()->getLocale())
                          ->first()?->name ?? $product->translations->first()?->name ?? $product->sku;
                      }),

                    Infolists\Components\TextEntry::make('product.sku')
                      ->label(__('app.fields.sku'))
                      ->badge()
                      ->color('gray')
                      ->prefix('SKU: '),

                    Infolists\Components\Grid::make(4)
                      ->schema([
                        Infolists\Components\TextEntry::make('quantity')
                          ->label(__('app.fields.quantity'))
                          ->badge()
                          ->color('info')
                          ->prefix('Qty: '),

                        Infolists\Components\TextEntry::make('price')
                          ->label(__('app.fields.unit_price'))
                          ->money('USD')
                          ->color('primary'),

                        Infolists\Components\TextEntry::make('discount_price')
                          ->label(__('app.fields.discount'))
                          ->money('USD')
                          ->color('danger')
                          ->prefix('-')
                          ->visible(fn($state): bool => !empty($state) && $state > 0),

                        Infolists\Components\TextEntry::make('total_line')
                          ->label(__('app.fields.line_total'))
                          ->state(
                            fn($record): float => ($record->price - ($record->discount_price ?? 0)) * $record->quantity
                          )
                          ->money('USD')
                          ->weight('bold')
                          ->color('success'),
                      ]),
                  ])->grow(),
                ]),

                // Infolists\Components\Separator::make(),
              ])
              ->columns(1),

            // Order Totals
            Infolists\Components\Grid::make(1)
              ->schema([
                Infolists\Components\Group::make([
                  Infolists\Components\Split::make([
                    Infolists\Components\TextEntry::make('subtotal')
                      ->label(__('app.fields.subtotal'))
                      ->state(fn(Order $record): float => $record->subtotal)
                      ->money('USD'),

                    Infolists\Components\TextEntry::make('total_discount')
                      ->label(__('app.fields.total_discount'))
                      ->state(fn(Order $record): float => $record->total_discount)
                      ->money('USD')
                      ->color('danger')
                      ->prefix('-')
                      ->visible(fn(Order $record): bool => $record->total_discount > 0),

                    Infolists\Components\TextEntry::make('total_amount')
                      ->label(__('app.fields.final_total'))
                      ->money('USD')
                      ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                      ->weight('bold')
                      ->color('success'),
                  ])->from('md'),
                ])
                  ->extraAttributes(['class' => 'border-t pt-4 mt-4']),
              ]),
          ]),

        // Payment Information Section  
        Infolists\Components\Section::make(__('app.sections.payment_information'))
          ->icon('heroicon-o-credit-card')
          ->description(__('app.sections.payment_information_description'))
          ->schema([
            Infolists\Components\RepeatableEntry::make('payments')
              ->label(__('app.fields.payment_transactions'))
              ->schema([
                Infolists\Components\Grid::make(4)
                  ->schema([
                    Infolists\Components\TextEntry::make('transaction_id')
                      ->label(__('app.fields.transaction_id'))
                      ->copyable()
                      ->weight('bold')
                      ->icon('heroicon-o-hashtag'),

                    Infolists\Components\TextEntry::make('amount')
                      ->label(__('app.fields.amount'))
                      ->money('USD')
                      ->color('success'),

                    Infolists\Components\TextEntry::make('status')
                      ->label(__('app.fields.payment_status'))
                      ->badge()
                      ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                      }),

                    Infolists\Components\TextEntry::make('created_at')
                      ->label(__('app.fields.processed_at'))
                      ->dateTime('M j, Y g:i A')
                      ->icon('heroicon-o-clock'),
                  ]),
              ])
              ->visible(fn(Order $record): bool => $record->payments->isNotEmpty()),

            // Show message for cash on delivery
            Infolists\Components\TextEntry::make('cash_on_delivery_note')
              ->label('')
              ->state(__('app.messages.cash_on_delivery_note'))
              ->color('info')
              ->icon('heroicon-o-information-circle')
              ->visible(
                fn(Order $record): bool =>
                $record->payment_method === 'cash_on_delivery' &&
                  $record->payments->isEmpty()
              ),
          ])
          ->collapsible(),

        // Admin Notes Section
        Infolists\Components\Section::make(__('app.sections.admin_notes'))
          ->icon('heroicon-o-document-text')
          ->schema([
            Infolists\Components\TextEntry::make('admin_notes')
              ->label('')
              ->placeholder(__('app.placeholders.no_notes_added'))
              ->columnSpanFull(),
          ])
          ->visible(fn(Order $record): bool => !empty($record->admin_notes))
          ->collapsible(),
      ]);
  }
}
