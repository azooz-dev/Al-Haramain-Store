<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use App\Models\Order\Order;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use App\Models\Payment\Payment;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\OrderResource;
use App\Filament\Concerns\SendsFilamentNotifications;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewOrder extends ViewRecord
{
  use SendsFilamentNotifications;
  protected static string $resource = OrderResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Action::make('change_status')
        ->label(__('app.actions.change_status'))
        ->icon('heroicon-o-adjustments-horizontal')
        ->color('info')
        ->form([
          \Filament\Forms\Components\Select::make('status')
            ->label(__('app.fields.new_status'))
            ->options([
              Order::PENDING => __('app.status.pending'),
              Order::PROCESSING => __('app.status.processing'),
              Order::SHIPPED => __('app.status.shipped'),
              Order::DELIVERED => __('app.status.delivered'),
              Order::CANCELLED => __('app.status.cancelled'),
              Order::REFUNDED => __('app.status.refunded'),
            ])
            ->required()
            ->native(false),
        ])
        ->action(function (array $data) {
          /** @var Order $order */
          $order = $this->record;
          $order->update(['status' => $data['status']]);

          return self::buildSuccessNotification(
            __('app.messages.order.status_updated'),
            __('app.messages.order.order_status_updated', ['num' => $order->order_number, 'status' => Str::headline($order->status)])
          );
        })
        ->requiresConfirmation(),

      Action::make('download_invoice')
        ->label(__('app.actions.download_invoice'))
        ->icon('heroicon-o-printer')
        ->color('success')
        ->action(function (): StreamedResponse {
          /** @var Order $order */
          $order = $this->record;

          $html = view('invoices.order', [
            'order' => $order->loadMissing(['user', 'address', 'items.product', 'payments', 'coupon']),
          ])->render();

          return response()->streamDownload(function () use ($html) {
            echo $html;
          }, 'invoice-' . $order->order_number . '.html');
        })
        ->requiresConfirmation(false)
        ->modalHeading(__('app.actions.generate_invoice')),
    ];
  }
}
