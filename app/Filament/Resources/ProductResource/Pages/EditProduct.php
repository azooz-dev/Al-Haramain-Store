<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;
use Modules\Catalog\Services\Product\ProductService;
use Modules\Catalog\Traits\HasProductTranslations;
use App\Filament\Concerns\SendsFilamentNotifications;

class EditProduct extends EditRecord
{
    use HasProductTranslations, SendsFilamentNotifications;
    protected static string $resource = ProductResource::class;

    private array $translationData = [];
    private ?array $categoryIds = null;

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
            unset($formData['stock'], $formData['reviews']);

            // Remove images from custom form data - let Filament handle the relationship
            unset($formData['stock'], $formData['images']);
            // dd($formData);
            $this->form->fill($formData);
        }
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract category IDs if present (before translation extraction)
        $this->categoryIds = $data['categories'] ?? null;

        // Extract translation data and store for later use in service
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Remove categories from main data (will be synced by service)
        unset($extracted['main']['categories']);

        // Return main data without translations and categories (slug regeneration handled by service)
        return $extracted['main'];
    }

    /**
     * Handle record update using ProductService
     * 
     * This method uses ProductService to update the product and handle
     * translations and categories. The service handles slug regeneration if name changed.
     * 
     * @param Model $record - The product record
     * @param array $data - Cleaned form data (without translations and categories)
     * @return Model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $productService = app(ProductService::class);

        // Update product with translations and categories via service
        // Service handles slug regeneration if name changed, translation saving, and category sync
        $product = $productService->updateProduct($record->id, $data, $this->translationData, $this->categoryIds);

        return $product;
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
