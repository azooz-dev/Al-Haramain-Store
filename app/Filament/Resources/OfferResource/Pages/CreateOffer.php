<?php

namespace App\Filament\Resources\OfferResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Filament\Resources\OfferResource;
use App\Services\Offer\OfferService;
use App\Traits\Offer\HasOfferTranslations;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\SendsFilamentNotifications;

class CreateOffer extends CreateRecord
{
    use HasOfferTranslations, SendsFilamentNotifications;

    protected static string $resource = OfferResource::class;

    private array $translationData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Extract translation data before creating the offer
     * 
     * @param array $data - Raw form data from Filament
     * @return array - Cleaned data for offer creation (without translations)
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract translation data and store for later use in service
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Return main data without translations (translation saving handled by service)
        return $extracted['main'];
    }

    /**
     * Handle record creation using OfferService
     * 
     * This method uses OfferService to create the offer and handle
     * translations. The service handles translation saving.
     * 
     * @param array $data - Cleaned form data (without translations)
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        $offerService = app(OfferService::class);

        // Create offer with translations via service
        // Service handles translation saving
        $offer = $offerService->createOffer($data, $this->translationData);

        return $offer;
    }

    protected function getSavedNotification(): ?Notification
    {
        $offerService = app(OfferService::class);
        return self::buildSuccessNotification(
            __('app.messages.offer.created_success'),
            __('app.messages.offer.created_success_body', ['name' => $offerService->getTranslatedName($this->record)])
        );
    }
}
