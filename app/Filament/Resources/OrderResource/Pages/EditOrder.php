<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use App\Models\Order\Order;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\OrderResource;
use App\Filament\Concerns\SendsFilamentNotifications;

class EditOrder extends EditRecord
{
    use SendsFilamentNotifications;
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_order')
                ->label(__('app.actions.view_order'))
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn(): string => $this->getResource()::getUrl('view', ['record' => $this->record])),

            Actions\DeleteAction::make()
                ->visible(
                    fn(): bool =>
                    in_array($this->record->status, [Order::CANCELLED, Order::REFUNDED])
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
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
        }
    }

    public function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.order.status_updated'),
            __('app.messages.order.order_status_updated', ['num' => $this->record->order_number, 'status' => $newStatus])
        );
    }
    public static function getRelations(): array
    {
        return [];
    }
}
