<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Traits\Category\HasCategoryTranslations;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use HasCategoryTranslations;

    protected static string $resource = CategoryResource::class;

    /**
     * Holds EN/AR translation data across the create lifecycle.
     */
    private array $translationData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Generate slug from English name (or Arabic if English not available)
        $mainData = $extracted['main'];
        $mainData['slug'] = $this->generateSlugFromName($mainData['en']['name']);

        return $mainData;
    }

    protected function afterCreate(): void
    {
        $this->saveTranslations($this->translationData);
    }
}
