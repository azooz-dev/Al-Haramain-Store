<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Actions;
use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\SendsFilamentNotifications;

class CreateCoupon extends CreateRecord
{
    use SendsFilamentNotifications;
    protected static string $resource = CouponResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess(__('app.messages.coupon.created_success'), __('app.messages.coupon.created_success_body', ['name' => $this->record->name]));
    }
}
