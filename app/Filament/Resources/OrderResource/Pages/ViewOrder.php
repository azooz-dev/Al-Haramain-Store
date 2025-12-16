<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Modules\Order\Entities\Order\Order;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\OrderResource;
use Modules\Order\Services\Order\OrderService;
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
            ->options(function () {
              /** @var Order $order */
              $order = $this->record;
              $orderService = app(OrderService::class);
              return $orderService->getAvailableStatuses($order);
            })
            ->required()
            ->native(false)
            ->default(fn() => $this->record->status)
            ->helperText(function () {
              /** @var Order $order */
              $order = $this->record;
              $orderService = app(OrderService::class);
              $availableStatuses = $orderService->getAvailableStatuses($order);

              if (count($availableStatuses) === 1) {
                return __('app.forms.order.status_terminal_state');
              }

              return __('app.forms.order.status_available_transitions');
            }),
        ])
        ->action(function (array $data) {
          /** @var Order $order */
          $order = $this->record;
          $orderService = app(OrderService::class);

          // Update status via service (includes validation and logging)
          $updatedOrder = $orderService->updateOrderStatus($order->id, $data['status']);

          // Refresh record to get updated status
          $order->refresh();

          return self::buildSuccessNotification(
            __('app.messages.order.status_updated'),
            __('app.messages.order.order_status_updated', ['num' => $order->order_number, 'status' => Str::headline($updatedOrder->status)])
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
            'order' => $order->loadMissing(['user', 'address', 'items.orderable.translations', 'items.variant', 'items.color', 'payments', 'coupon']),
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
