<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Filament\Resources\CouponResource;
use App\Services\Coupon\CouponService;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\SendsFilamentNotifications;

class CreateCoupon extends CreateRecord
{
    use SendsFilamentNotifications;
    protected static string $resource = CouponResource::class;

    /**
     * Handle record creation using CouponService
     * 
     * @param array $data
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        $couponService = app(CouponService::class);
        return $couponService->createCoupon($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess(__('app.messages.coupon.created_success'), __('app.messages.coupon.created_success_body', ['name' => $this->record->name]));
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.coupon.created_success'),
            __('app.messages.coupon.created_success_body', ['name' => $this->record->name])
        );
    }
}
