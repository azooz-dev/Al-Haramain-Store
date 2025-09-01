<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CategoryResource;
use App\Traits\Category\HasCategoryTranslations;
use App\Filament\Concerns\SendsFilamentNotifications;

/**
 * EditCategory Page
 * 
 * Filament page for editing existing categories with translations.
 * 
 * This page handles:
 * - Form population with existing category and translation data
 * - Form data processing for category updates
 * - Conditional slug regeneration when names change
 * - Translation data extraction and saving
 * - Integration with the HasCategoryTranslations trait
 * 
 * Workflow:
 * 1. fillForm() loads existing category and translation data
 * 2. User modifies form data
 * 3. mutateFormDataBeforeSave() processes changes and regenerates slug if needed
 * 4. Category is updated
 * 5. afterSave() saves the updated translations
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
     * mutateFormDataBeforeSave() and afterSave() methods.
     * It ensures translations are saved after the main category is updated.
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
     * This method is called by Filament before updating the category record.
     * It performs several tasks:
     * 1. Extracts translation data from the form and stores it for later use
     * 2. Checks if the English name has changed
     * 3. Regenerates the slug if the name has changed (to maintain URL consistency)
     * 
     * @param array $data - Raw form data from Filament
     * @return array - Cleaned data for category update (without translations)
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract translation data and store for later saving
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Check if name changed and regenerate slug if needed
        $mainData = $extracted['main'];
        $currentName = $this->translationService->getTranslatedName($this->record);

        // If name changed, regenerate slug
        if ($this->translationData['en']['name'] !== $currentName) {
            $mainData['slug'] = $this->generateSlugFromName($this->translationData['en']['name']);
        }

        return $mainData;
    }

    /**
     * Save translations after category update
     * 
     * This method is called by Filament after the category record is updated.
     * It saves the translation data that was extracted earlier to the
     * category_translations table.
     * 
     * The translations are saved separately because they belong to a different
     * table and need the category ID to establish the relationship.
     */
    protected function afterSave(): void
    {
        $this->saveTranslations($this->translationData);
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
