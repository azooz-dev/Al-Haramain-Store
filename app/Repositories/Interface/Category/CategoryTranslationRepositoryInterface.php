<?php

namespace App\Repositories\Interface\Category;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use Illuminate\Support\Collection;

/**
 * CategoryTranslationRepositoryInterface
 * 
 * This interface defines the contract for category translation data access operations.
 * It abstracts the database layer and provides a clean interface for translation-related queries.
 * 
 * Key Responsibilities:
 * - CRUD operations for category translations
 * - Retrieving translations by locale
 * - Saving and updating translation records
 * - Searching translations across different fields
 * 
 * Implementation:
 * - EloquentCategoryTranslationRepository provides the concrete implementation
 * - Can be easily swapped for testing or different data sources
 * 
 * @package App\Repositories\Interface\Category
 */
interface CategoryTranslationRepositoryInterface
{
  /**
   * Get all translations for a specific category
   * 
   * Retrieves all translation records (English, Arabic, etc.) for a given category.
   * Useful for bulk operations or when you need all translations at once.
   * 
   * @param Category $category - The category to get translations for
   * @return Collection - Collection of CategoryTranslation models
   */
  public function getTranslationsForCategory(Category $category): Collection;

  /**
   * Get translation for a specific locale
   * 
   * Retrieves a single translation record for a specific locale (e.g., 'en', 'ar').
   * Returns null if no translation exists for that locale.
   * 
   * @param Category $category - The category to get translation for
   * @param string $locale - The locale to retrieve (e.g., 'en', 'ar')
   * @return CategoryTranslation|null - The translation model or null if not found
   */
  public function getTranslationByLocale(Category $category, string $locale): ?CategoryTranslation;

  /**
   * Save a new translation
   * 
   * Creates a new translation record for a category and locale.
   * Use this for creating new translations that don't exist yet.
   * 
   * @param Category $category - The category to save translation for
   * @param string $locale - The locale for this translation (e.g., 'en', 'ar')
   * @param array $data - Translation data (name, description)
   * @return CategoryTranslation - The newly created translation model
   */
  public function saveTranslation(Category $category, string $locale, array $data): CategoryTranslation;

  /**
   * Update or create a translation
   * 
   * Updates an existing translation or creates a new one if it doesn't exist.
   * This is the preferred method for saving translations as it handles both cases.
   * 
   * @param Category $category - The category to save translation for
   * @param string $locale - The locale for this translation (e.g., 'en', 'ar')
   * @param array $data - Translation data (name, description)
   * @return CategoryTranslation - The updated or created translation model
   */
  public function updateOrCreateTranslation(Category $category, string $locale, array $data): CategoryTranslation;

  /**
   * Delete all translations for a category
   * 
   * Removes all translation records associated with a specific category.
   * Used when deleting a category to clean up related translation data.
   * 
   * @param Category $category - The category whose translations should be deleted
   * @return bool - True if deletion was successful, false otherwise
   */
  public function deleteTranslationsForCategory(Category $category): bool;

  /**
   * Search translations by field value
   * 
   * Performs a search across translation records for a specific field.
   * Useful for finding translations that contain certain text.
   * 
   * @param string $search - The search term to look for
   * @param string $field - The field to search in (default: 'name')
   * @return Collection - Collection of matching translation models
   */
  public function searchTranslations(string $search, string $field = 'name'): Collection;
}
