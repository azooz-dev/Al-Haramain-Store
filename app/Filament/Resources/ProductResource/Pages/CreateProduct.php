<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;
use Modules\Catalog\Contracts\ProductServiceInterface;
use Modules\Catalog\Traits\HasProductTranslations;
use App\Filament\Concerns\SendsFilamentNotifications;

class CreateProduct extends CreateRecord
{
    use HasProductTranslations, SendsFilamentNotifications;

    protected static string $resource = ProductResource::class;

    private array $translationData = [];
    private ?array $categoryIds = null;

    public function __construct(
        protected ProductServiceInterface $productService
    ) {
        parent::__construct();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract category IDs if present (before translation extraction)
        $this->categoryIds = $data['categories'] ?? null;

        // Extract translation data and store for later use in service
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Remove categories from main data (will be synced by service)
        unset($extracted['main']['categories']);

        // Return main data without translations and categories (slug generation handled by service)
        return $extracted['main'];
    }

    /**
     * Handle record creation using ProductService
     * 
     * This method uses ProductService to create the product and handle
     * translations and categories. The service handles slug generation.
     * 
     * @param array $data - Cleaned form data (without translations and categories)
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Create product with translations and categories via service
        // Service handles slug generation, translation saving, and category sync
        $product = $this->productService->createProduct($data, $this->translationData, $this->categoryIds);

        return $product;
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.product.created_success'),
            __('app.messages.product.created_success_body', ['name' => $this->record->translations->where('local', app()->getLocale())->first()?->name
                ?? $this->record->translations->first()?->name
                ?? $this->record->slug])
        );
    }
}
