<?php

namespace Modules\Admin\Listeners;

use Modules\Order\Events\OrderCreated;
use Modules\Admin\Entities\Admin;
use Filament\Notifications\Notification;

class SendOrderNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $admins = Admin::role('super_admin')->get();

        foreach ($admins as $admin) {
            Notification::make()
                ->title('New Order #' . $order->order_number)
                ->body($order->user?->name . __('app.messages.order.new_order') . "SAR " . number_format($order->total_amount, 2))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->label('View Order')
                        ->url(route('filament.admin.resources.orders.view', $order->id)),
                ])->sendToDatabase($admin, isEventDispatched: true);
        }
    }
}

