<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;
use App\Traits\Product\HasProductTranslations;
use App\Filament\Concerns\SendsFilamentNotifications;

class CreateProduct extends CreateRecord
{
    use HasProductTranslations, SendsFilamentNotifications;

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
        $this->notifySuccess(__('app.messages.product.created_success'), __('app.messages.product.created_success_body', ['name' => $this->record->translations->where('local', app()->getLocale())->first()?->name
            ?? $this->record->translations->first()?->name
            ?? $this->record->slug]));
    }
}
