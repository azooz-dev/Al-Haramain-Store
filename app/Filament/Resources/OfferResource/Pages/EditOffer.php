<?php

namespace App\Filament\Resources\OfferResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\OfferResource;
use App\Services\Offer\OfferService;
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

    /**
     * Extract translation data before saving the offer
     * 
     * @param array $data - Raw form data from Filament
     * @return array - Cleaned data for offer update (without translations)
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract translation data and store for later use in service
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Return main data without translations (translation saving handled by service)
        return $extracted['main'];
    }

    /**
     * Handle record update using OfferService
     * 
     * This method uses OfferService to update the offer and handle
     * translations. The service handles translation saving.
     * 
     * @param Model $record
     * @param array $data - Cleaned form data (without translations)
     * @return Model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $offerService = app(OfferService::class);

        // Update offer with translations via service
        // Service handles translation saving
        $offer = $offerService->updateOffer($record->id, $data, $this->translationData);

        return $offer;
    }

    protected function getSavedNotification(): ?Notification
    {
        $offerService = app(OfferService::class);
        return self::buildSuccessNotification(
            __('app.messages.offer.updated_success'),
            __('app.messages.offer.updated_success_body', ['name' => $offerService->getTranslatedName($this->record)])
        );
    }
}
