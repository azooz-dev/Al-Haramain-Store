<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_order')
                ->label(__('app.actions.view_order'))
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn(): string => $this->getResource()::getUrl('view', ['record' => $this->record])),

            // Actions\Action::make('print_invoice')
            //     ->label(__('app.actions.print_invoice'))
            //     ->icon('heroicon-o-printer')
            //     ->color('success')
            //     // ->url(fn(): string => route('admin.orders.invoice', $this->record))
            //     ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(
                    fn(): bool =>
                    in_array($this->record->status, [Order::CANCELLED, Order::REFUNDED])
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function beforeSave(): void
    {
        // Track status changes
        if ($this->record->isDirty('status')) {
            $oldStatus = $this->record->getOriginal('status');
            $newStatus = $this->record->status;

            // Log status change
            \Illuminate\Support\Facades\Log::info('Order status changed', [
                'order_id' => $this->record->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => auth()->id(),
            ]);

            // Send notification to customer if needed
            // $this->notifyCustomerOfStatusChange($oldStatus, $newStatus);
        }
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title(__('app.notifications.order_updated.title'))
            ->body(__('app.notifications.order_updated.body', [
                'order_number' => $this->record->order_number
            ]))
            ->success()
            ->send();
    }

    // private function notifyCustomerOfStatusChange(string $oldStatus, string $newStatus): void
    // {
    //     // Only notify for significant status changes
    //     $notifiableStatuses = [
    //         Order::PROCESSING,
    //         Order::SHIPPED,
    //         Order::DELIVERED,
    //         Order::CANCELLED,
    //         Order::REFUNDED,
    //     ];

    //     if (in_array($newStatus, $notifiableStatuses)) {
    //         // Here you would implement your notification logic
    //         // For example, sending an email or SMS to the customer

    //         // Example:
    //         // $this->record->user->notify(new OrderStatusChanged($this->record, $oldStatus, $newStatus));
    //     }
    // }
}
