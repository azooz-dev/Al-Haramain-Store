<?php

namespace App\Filament\Resources\OfferResource\Pages;

use Filament\Actions;
use App\Filament\Resources\OfferResource;
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
     * Process form data before creating the offer
     * 
     * This method is called by Filament before creating the offer record.
     * It performs two main tasks:
     * 1. Extracts translation data from the form and stores it for later use
     * 2. Generates a unique slug from the English name for the offer
     * 
     * @param array $data - Raw form data from Filament
     * @return array - Cleaned data for offer creation (without translations)
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract translation data and store for later saving
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        return $extracted['main'];
    }

    /**
     * Save translations after offer creation
     * 
     * This method is called by Filament after the offer record is created.
     * It saves the translation data that was extracted earlier to the
     * offer_translations table.
     * 
     * The translations are saved separately because they belong to a different
     * table and need the offer ID to establish the relationship.
     */
    protected function afterCreate(): void
    {
        $this->saveTranslations($this->translationData);
        $this->notifySuccess(__('app.messages.offer.created_success'), __('app.messages.offer.created_success_body', ['name' => $this->record->name]));
    }
}
