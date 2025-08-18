<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Traits\Category\HasCategoryTranslations;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    use HasCategoryTranslations;

    protected static string $resource = CategoryResource::class;

    /**
     * Holds EN/AR translation data across the save lifecycle.
     */
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
            $this->form->fill($formData);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Check if name changed and regenerate slug if needed
        $mainData = $extracted['main'];
        $currentName = $this->translationService->getTranslatedName($this->record);

        // If name changed, regenerate slug
        if ($mainData['en']['name'] !== $currentName) {
            $mainData['slug'] = $this->translationService->generateSlugForUpdate($mainData['en']['name'], $this->record->id);
        }

        return $mainData;
    }

    protected function afterSave(): void
    {
        $this->saveTranslations($this->translationData);
    }
}
