<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Modules\Catalog\Contracts\CategoryServiceInterface;
use App\Filament\Resources\CategoryResource;
use Modules\Catalog\Traits\HasCategoryTranslations;
use App\Filament\Concerns\SendsFilamentNotifications;

/**
 * EditCategory Page
 * 
 * Filament page for editing existing categories with translations.
 * 
 * This page handles:
 * - Form population with existing category and translation data
 * - Delegates business logic to CategoryService
 * 
 * Workflow:
 * 1. fillForm() loads existing category and translation data
 * 2. User modifies form data
 * 3. mutateFormDataBeforeSave() extracts translations
 * 4. handleRecordUpdate() uses CategoryService to update category and translations
 * 
 * @package App\Filament\Resources\CategoryResource\Pages
 */
class EditCategory extends EditRecord
{
    use HasCategoryTranslations, SendsFilamentNotifications;

    protected static string $resource = CategoryResource::class;

    /**
     * Holds EN/AR translation data across the save lifecycle.
     * 
     * This property stores the extracted translation data between
     * mutateFormDataBeforeSave() and handleRecordUpdate() methods.
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

    /**
     * Populate form with existing data
     * 
     * This method is called by Filament to populate the form with existing data.
     * It loads both the main category data (slug, image) and the translation data
     * (English and Arabic names/descriptions) into the form fields.
     * 
     * The method first calls the parent to load basic category data, then
     * uses the trait to load translation data and populate the form.
     */
    protected function fillForm(): void
    {
        // Load basic category data (slug, image, etc.)
        parent::fillForm();

        // Load and populate translation data if we have a record
        if ($record = $this->record) {
            $formData = $this->getTranslationData();
            $formData['en'] = ['name' => $formData['en']['title'], 'description' => $formData['en']['details']];
            $formData['ar'] = ['name' => $formData['ar']['title'], 'description' => $formData['ar']['details']];

            $this->form->fill($formData);
        }
    }

    /**
     * Process form data before saving the category
     * 
     * This method extracts translation data from the form and stores it
     * for use in handleRecordUpdate(). Slug regeneration is handled by service.
     * 
     * @param array $data - Raw form data from Filament
     * @return array - Cleaned data for category update (without translations)
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract translation data and store for later use in service
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Return main data without translations (slug regeneration handled by service)
        return $extracted['main'];
    }

    /**
     * Handle record update using CategoryService
     * 
     * This method uses CategoryService to update the category and handle
     * translations. The service handles slug regeneration if name changed.
     * 
     * @param Model $record - The category record
     * @param array $data - Cleaned form data (without translations)
     * @return Model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $categoryService = app(CategoryServiceInterface::class);

        // Update category with translations via service
        // Service handles slug regeneration if name changed and translation saving
        $category = $categoryService->updateCategory($record->id, $data, $this->translationData);

        return $category;
    }

    protected function getSavedNotification(): ?Notification
    {
        return self::buildSuccessNotification(
            __('app.messages.category.updated_success'),
            __('app.messages.category.updated_success_body', ['name' => $this->record->translations->where('local', app()->getLocale())->first()?->name
                ?? $this->record->translations->first()?->name
                ?? $this->record->slug])
        );
    }
}
