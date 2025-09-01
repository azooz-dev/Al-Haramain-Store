<?php

namespace App\Filament\Resources\OfferResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\OfferResource;
use App\Traits\Offer\HasOfferTranslations;
use App\Filament\Concerns\SendsFilamentNotifications;
use Filament\Notifications\Notification;

class EditOffer extends EditRecord
{
    use HasOfferTranslations, SendsFilamentNotifications;

    protected static string $resource = OfferResource::class;

    private array $translationData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        if ($record = $this->record) {
            $formData = $this->getTranslationData();
            $formData['en'] = ['name' => $formData['en']['title'], 'description' => $formData['en']['details']];
            $formData['ar'] = ['name' => $formData['ar']['title'], 'description' => $formData['ar']['details']];
            $formData['discount_type'] = $formData['discountType'];
            $formData['discount_amount'] = $formData['discountAmount'];
            $formData['start_date'] = $formData['startDate'];
            $formData['end_date'] = $formData['endDate'];
            $formData['image_path'] = $formData['picture'];

            unset($formData['discountType'], $formData['discountAmount'], $formData['startDate'], $formData['endDate'], $formData['picture']);
            $this->form->fill($formData);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        $mainData = $extracted['main'];
        $currentName = $this->translationService->getTranslatedName($this->record);

        return $mainData;
    }

    protected function afterSave(): void
    {
        $this->saveTranslations($this->translationData);
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.offer.updated_success'),
            __('app.messages.offer.updated_success_body', ['name' => $this->record->name])
        );
    }
}
