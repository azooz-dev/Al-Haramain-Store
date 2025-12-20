<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use Modules\Order\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\OrderResource;
use Modules\Order\Contracts\OrderServiceInterface;
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
                    in_array($this->record->status?->value ?? $this->record->status, [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Handle record update using OrderService
     * 
     * @param Model $record
     * @param array $data
     * @return Model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $orderService = app(OrderServiceInterface::class);
        return $orderService->updateOrder($record->id, $data);
    }

    public function getSavedNotification(): ?Notification
    {
        $status = $this->record->status;
        $statusValue = $status instanceof OrderStatus ? $status->value : $status;
        
        return self::buildSuccessNotification(
            __('app.messages.order.status_updated'),
            __('app.messages.order.order_status_updated', ['num' => $this->record->order_number, 'status' => $statusValue])
        );
    }
    public static function getRelations(): array
    {
        return [];
    }
}
