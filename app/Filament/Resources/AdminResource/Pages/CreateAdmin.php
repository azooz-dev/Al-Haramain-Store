<?php

namespace App\Filament\Resources\AdminResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\AdminResource;
use Modules\Admin\Contracts\AdminServiceInterface;
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
            __('app.messages.admin.created_success_body', ['name' => $this->record->name])
        );
    }

    /**
     * Handle record creation using AdminService
     * This ensures password hashing and role assignment are handled by the service layer
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $adminService = app(AdminServiceInterface::class);

        // Extract roles from data if present
        $roleIds = $data['roles'] ?? null;
        unset($data['roles']);

        // Create admin using service (handles password hashing)
        $admin = $adminService->createAdmin($data, $roleIds);

        return $admin;
    }
}
