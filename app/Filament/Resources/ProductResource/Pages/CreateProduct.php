<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Traits\Product\HasProductTranslations;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use HasProductTranslations;

    protected static string $resource = ProductResource::class;

    private array $translationData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //Extract translation data and store for later saving
        $extracted = $this->extractTranslationData($data);

        $this->translationData = $extracted['translations'];

        //Generate slug from English name 
        $mainData = $extracted['main'];
        $mainData['slug'] = $this->generateSlugFromName($extracted['translations']['en']['name']);

        return $mainData;
    }

    protected function afterCreate(): void
    {
        $this->saveTranslations($this->translationData);
    }
}
