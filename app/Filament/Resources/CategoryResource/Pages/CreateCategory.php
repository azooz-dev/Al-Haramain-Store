<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Traits\Category\HasCategoryTranslations;
use Filament\Resources\Pages\CreateRecord;

/**
 * CreateCategory Page
 * 
 * Filament page for creating new categories with translations.
 * 
 * This page handles:
 * - Form data processing for category creation
 * - Automatic slug generation from translation names
 * - Translation data extraction and saving
 * - Integration with the HasCategoryTranslations trait
 * 
 * Workflow:
 * 1. User fills form with translations (en/ar names/descriptions)
 * 2. mutateFormDataBeforeCreate() extracts translations and generates slug
 * 3. Category is created with generated slug
 * 4. afterCreate() saves the translations to separate table
 * 
 * @package App\Filament\Resources\CategoryResource\Pages
 */
class CreateCategory extends CreateRecord
{
    use HasCategoryTranslations;

    protected static string $resource = CategoryResource::class;

    /**
     * Holds EN/AR translation data across the create lifecycle.
     * 
     * This property stores the extracted translation data between
     * mutateFormDataBeforeCreate() and afterCreate() methods.
     * It ensures translations are saved after the main category is created.
     */
    private array $translationData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Process form data before creating the category
     * 
     * This method is called by Filament before creating the category record.
     * It performs two main tasks:
     * 1. Extracts translation data from the form and stores it for later use
     * 2. Generates a unique slug from the English name for the category
     * 
     * @param array $data - Raw form data from Filament
     * @return array - Cleaned data for category creation (without translations)
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract translation data and store for later saving
        $extracted = $this->extractTranslationData($data);
        $this->translationData = $extracted['translations'];

        // Generate slug from English name
        $mainData = $extracted['main'];

        $mainData['slug'] = $this->generateSlugFromName($extracted['translations']['en']['name']);

        return $mainData;
    }

    /**
     * Save translations after category creation
     * 
     * This method is called by Filament after the category record is created.
     * It saves the translation data that was extracted earlier to the
     * category_translations table.
     * 
     * The translations are saved separately because they belong to a different
     * table and need the category ID to establish the relationship.
     */
    protected function afterCreate(): void
    {
        $this->saveTranslations($this->translationData);
    }
}
