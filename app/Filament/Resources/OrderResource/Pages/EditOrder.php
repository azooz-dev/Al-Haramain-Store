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

    public function getSavedNotification(): Notification
    {
        $oldStatus = $this->record->getOriginal('status');
        $newStatus = $this->record->status;

        return Notification::make()
            ->title(__('app.notifications.order_updated.title'))
            ->body(__('app.notifications.order_updated.body', [
                'order_number' => $this->record->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]))
            ->success()
            ->send();
    }
    public static function getRelations(): array
    {
        return [];
    }
}
