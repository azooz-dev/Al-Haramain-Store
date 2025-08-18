<?php

namespace App\Traits;

use App\Services\CategoryTranslationService;

/**
 * HasCategoryTranslations Trait
 * 
 * This trait serves as a bridge between Filament admin panel pages and the CategoryTranslationService.
 * It provides reusable methods for handling category translations in Filament forms and pages.
 * 
 * Key Responsibilities:
 * - Extract translation data from Filament form submissions
 * - Populate Filament forms with existing translation data
 * - Save translations after category creation/updates
 * - Generate slugs from translation data
 * 
 * Usage:
 * - Include this trait in Filament pages that handle category translations
 * - Provides consistent translation handling across Create, Edit, and View pages
 * 
 * @package App\Traits
 */
trait HasCategoryTranslations
{
  /**
   * CategoryTranslationService instance
   * 
   * Injected via constructor to handle all business logic related to translations.
   * This maintains separation of concerns - the trait handles UI logic, service handles business logic.
   */
  protected CategoryTranslationService $translationService;

  /**
   * Constructor - Initialize the translation service
   * 
   * Resolves the CategoryTranslationService from Laravel's service container.
   * This enables dependency injection and makes the trait testable.
   */
  public function __construct()
  {
    $this->translationService = app(CategoryTranslationService::class);
  }

  /**
   * Get translation data for form population
   * 
   * Retrieves existing translation data for a category and formats it
   * for use in Filament forms. Used when editing or viewing existing categories.
   * 
   * Returns data in the format expected by Filament forms:
   * [
   *   'slug' => 'category-slug',
   *   'image' => 'path/to/image.jpg',
   *   'en' => ['name' => 'English Name', 'description' => 'English Description'],
   *   'ar' => ['name' => 'Arabic Name', 'description' => 'Arabic Description']
   * ]
   * 
   * @return array - Formatted data for Filament form population
   */
  protected function getTranslationData(): array
  {
    // Safety check - ensure we have a record to work with
    if (!$this->record) {
      return [];
    }

    return $this->translationService->getFormData($this->record);
  }

  /**
   * Save translations for a category
   * 
   * Processes translation data and saves it to the database using the service layer.
   * This method is called after the main category record has been created/updated.
   * 
   * The translation data should be in the format:
   * [
   *   'en' => ['name' => 'English Name', 'description' => 'English Description'],
   *   'ar' => ['name' => 'Arabic Name', 'description' => 'Arabic Description']
   * ]
   * 
   * @param array $translationData - Translation data to save
   * @return void
   */
  protected function saveTranslations(array $translationData): void
  {
    // Safety check - ensure we have a record to work with
    if (!$this->record) {
      return;
    }

    // Delegate to service layer for actual saving logic
    $this->translationService->saveTranslations($this->record, $translationData);
  }

  /**
   * Extract translation data from form submission
   * 
   * Separates translation data from the main form data and returns both parts.
   * This is used in Filament's mutateFormDataBeforeCreate/Save methods to:
   * 1. Extract translation data for later saving
   * 2. Return clean main data for category creation/update
   * 
   * Input format (from Filament form):
   * [
   *   'slug' => 'category-slug',
   *   'image' => 'path/to/image.jpg',
   *   'en' => ['name' => 'English Name', 'description' => 'English Description'],
   *   'ar' => ['name' => 'Arabic Name', 'description' => 'Arabic Description']
   * ]
   * 
   * Output format:
   * [
   *   'main' => ['slug' => 'category-slug', 'image' => 'path/to/image.jpg'],
   *   'translations' => [
   *     'en' => ['name' => 'English Name', 'description' => 'English Description'],
   *     'ar' => ['name' => 'Arabic Name', 'description' => 'Arabic Description']
   *   ]
   * ]
   * 
   * @param array $data - Raw form data from Filament
   * @return array - Separated main data and translation data
   */
  protected function extractTranslationData(array $data): array
  {
    // Extract translation data for both supported locales
    $translationData = [
      'en' => [
        'name' => $data['en']['name'] ?? null,
        'description' => $data['en']['description'] ?? null,
      ],
      'ar' => [
        'name' => $data['ar']['name'] ?? null,
        'description' => $data['ar']['description'] ?? null,
      ],
    ];

    // Remove translation data from main data array to keep it clean
    // This prevents translation data from being saved to the main category table
    unset($data['en'], $data['ar']);

    return [
      'main' => $data,           // Clean data for category table
      'translations' => $translationData  // Translation data for separate table
    ];
  }

  /**
   * Generate slug from translation data
   * 
   * Creates a unique, URL-friendly slug from the translation names.
   * Delegates to the service layer for the actual slug generation logic.
   * 
   * Priority order for slug generation:
   * 1. English name (preferred)
   * 2. Arabic name (fallback)
   * 3. Error if neither exists
   * 
   * @param array $translationData - Translation data containing names
   * @return string - Generated unique slug
   */
  protected function generateSlugFromTranslations(array $translationData): string
  {
    return $this->translationService->generateSlugFromTranslations($translationData);
  }
}
