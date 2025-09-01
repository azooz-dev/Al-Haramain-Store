<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;
use App\Traits\Product\HasProductTranslations;
use App\Filament\Concerns\SendsFilamentNotifications;

class EditProduct extends EditRecord
{
    use HasProductTranslations, SendsFilamentNotifications;
    protected static string $resource = ProductResource::class;

    private array $translationData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function fillForm(): void
    {
        //Load a basic product (name, slug, description..etc)
        Parent::fillForm();

        //Load and populate translation data if we have a record
        if ($record = $this->record) {
            // Only fill translation data, let Filament handle the relationship data
            $formData = $this->getTranslationData();
            $formData['en'] = ['name' => $formData['en']['title'], 'description' => $formData['en']['details']];
            $formData['ar'] = ['name' => $formData['ar']['title'], 'description' => $formData['ar']['details']];
            $formData['quantity'] = $formData['stock'];
            unset($formData['stock']);

            // Remove images from custom form data - let Filament handle the relationship
            unset($formData['stock'], $formData['images']);
            // dd($formData);
            $this->form->fill($formData);
        }
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        //Extract Translation data if we have a record
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        //Check if name changed, regenerated slug if needed
        $mainData = $extracted['main'];
        $currentName = $this->translationService->getTranslatedName($this->record);

        //If name changed, regenerated slug
        if ($this->translationData['en']['name'] !== $currentName) {
            $mainData['slug'] = $this->generateSlugFromName($this->translationData['en']['name']);
        }

        return $mainData;
    }

    protected function afterSave(): void
    {
        $this->saveTranslations($this->translationData);
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.product.updated_success'),
            __('app.messages.product.updated_success_body', ['name' => $this->record->translations->where('local', app()->getLocale())->first()?->name
                ?? $this->record->translations->first()?->name
                ?? $this->record->slug])
        );
    }
}
