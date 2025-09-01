<?php

namespace App\Filament\Resources\AdminResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\SendsFilamentNotifications;

class CreateAdmin extends CreateRecord
{
    use SendsFilamentNotifications;
    protected static string $resource = AdminResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.admin.created_success'),
            __('app.messages.admin.created_success_body', ['name' => $this->record->first_name . ' ' . $this->record->last_name])
        );
    }
}
