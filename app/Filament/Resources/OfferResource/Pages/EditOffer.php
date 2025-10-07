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

            $safeFormData = [
                'en' => [
                    'name' => $formData['en']['title'] ?? '',
                    'description' => $formData['en']['details'] ?? ''
                ],
                'ar' => [
                    'name' => $formData['ar']['title'] ?? '',
                    'description' => $formData['ar']['details'] ?? ''
                ],
                'products_total_price' => $formData['productsTotalPrice'] ?? 0,
                'offer_price' => $formData['offerPrice'] ?? 0,
                'start_date' => $formData['startDate'] ?? null,
                'end_date' => $formData['endDate'] ?? null,
                'image_path' => $formData['picture'] ?? null,
                'status' => $formData['status'] ?? null,
            ];

            $this->form->fill($safeFormData);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        $mainData = $extracted['main'];
        $this->translationService->getTranslatedName($this->record);

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
