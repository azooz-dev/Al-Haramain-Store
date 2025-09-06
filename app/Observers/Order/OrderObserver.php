<?php

namespace App\Observers\Order;

use App\Models\Admin\Admin;
use App\Models\Order\Order;
use Filament\Notifications\Notification;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     */
    public function created(Order $order): void
    {
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

    /**
     * Handle the order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }
}
