<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CouponResource;
use App\Filament\Concerns\SendsFilamentNotifications;

class EditCoupon extends EditRecord
{
    use SendsFilamentNotifications;
    protected static string $resource = CouponResource::class;

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
}
