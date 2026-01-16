<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CouponResource;
use Modules\Coupon\Contracts\CouponServiceInterface;
use App\Filament\Concerns\SendsFilamentNotifications;

class EditCoupon extends EditRecord
{
    use SendsFilamentNotifications;
    protected static string $resource = CouponResource::class;

    /**
     * Handle record update using CouponService
     * 
     * @param Model $record
     * @param array $data
     * @return Model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $couponService = app(CouponServiceInterface::class);
        return $couponService->updateCoupon($record->id, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.coupon.updated_success'),
            __('app.messages.coupon.updated_success_body', ['name' => $this->record->name])
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
