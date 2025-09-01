<?php

namespace App\Filament\Resources\AdminResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\AdminResource;
use App\Filament\Concerns\SendsFilamentNotifications;

class EditAdmin extends EditRecord
{
    use SendsFilamentNotifications;
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.admin.updated_success'),
            __('app.messages.admin.updated_success_body', ['name' => $this->record->first_name . ' ' . $this->record->last_name])
        );
    }
}
