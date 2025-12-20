<?php

namespace App\Filament\Resources\AdminResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\AdminResource;
use Modules\Admin\Contracts\AdminServiceInterface;
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
            __('app.messages.admin.updated_success_body', ['name' => $this->record->name])
        );
    }

    /**
     * Handle record update using AdminService
     * This ensures password hashing and role synchronization are handled by the service layer
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $adminService = app(AdminServiceInterface::class);

        // Extract roles from data if present
        $roleIds = $data['roles'] ?? null;
        unset($data['roles']);

        // Update admin using service (handles password hashing if password is provided)
        $admin = $adminService->updateAdmin($record->id, $data, $roleIds);

        return $admin;
    }
}
