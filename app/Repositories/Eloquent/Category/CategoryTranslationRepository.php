<?php

namespace App\Repositories\Eloquent\Category;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * EloquentCategoryTranslationRepository
 * 
 * Eloquent implementation of CategoryTranslationRepositoryInterface.
 * Provides concrete database operations for category translations using Laravel's Eloquent ORM.
 * 
 * This implementation handles:
 * - Direct database queries for translation records
 * - Relationship-based queries using Eloquent relationships
 * - Complex queries with joins and where clauses
 * - Update or create operations for translations
 * 
 * @package App\Repositories\Eloquent\Category
 */
class CategoryTranslationRepository implements CategoryTranslationRepositoryInterface
{
  /**
   * Get all translations for a specific category
   * 
   * Uses the Eloquent relationship to retrieve all translation records
   * for a given category. This leverages the hasMany relationship defined
   * in the Category model.
   * 
   * @param Category $category - The category to get translations for
   * @return Collection - Collection of CategoryTranslation models
   */
  public function getTranslationsForCategory(Category $category): Collection
  {
    return $category->translations;
  }

  /**
   * Get translation for a specific locale
   * 
   * Uses the Eloquent relationship with a where clause to find
   * a specific translation for a given locale. Returns null if not found.
   * 
   * @param Category $category - The category to get translation for
   * @param string $locale - The locale to retrieve (e.g., 'en', 'ar')
   * @return CategoryTranslation|null - The translation model or null if not found
   */
  public function getTranslationByLocale(Category $category, string $locale): ?CategoryTranslation
  {
    return $category->translations()->where('local', $locale)->first();
  }

  /**
   * Save a new translation
   * 
   * Creates a new translation record using the Eloquent relationship.
   * This method assumes the translation doesn't exist yet.
   * 
   * @param Category $category - The category to save translation for
   * @param string $locale - The locale for this translation (e.g., 'en', 'ar')
   * @param array $data - Translation data (name, description)
   * @return CategoryTranslation - The newly created translation model
   */
  public function saveTranslation(Category $category, string $locale, array $data): CategoryTranslation
  {
    return $category->translations()->create([
      'local' => $locale,
      'name' => $data['name'] ?? '',
      'description' => $data['description'] ?? '',
    ]);
  }

  /**
   * Update or create a translation
   * 
   * Uses Eloquent's updateOrCreate method to either update an existing
   * translation or create a new one if it doesn't exist. This is the
   * preferred method for saving translations as it handles both cases.
   * 
   * The method uses a composite key of category_id and local to determine
   * whether to update or create.
   * 
   * @param Category $category - The category to save translation for
   * @param string $locale - The locale for this translation (e.g., 'en', 'ar')
   * @param array $data - Translation data (name, description)
   * @return CategoryTranslation - The updated or created translation model
   */
  public function updateOrCreateTranslation(Category $category, string $locale, array $data): CategoryTranslation
  {
    return CategoryTranslation::updateOrCreate(
      [
        'category_id' => $category->id,
        'local' => $locale,
      ],
      [
        'name' => $data['name'] ?? '',
        'description' => $data['description'] ?? '',
      ]
    );
  }

  /**
   * Delete all translations for a category
   * 
   * Uses the Eloquent relationship to delete all translation records
   * associated with a specific category. This is typically called
   * when deleting a category to clean up related data.
   * 
   * @param Category $category - The category whose translations should be deleted
   * @return bool - True if deletion was successful, false otherwise
   */
  public function deleteTranslationsForCategory(Category $category): bool
  {
    return $category->translations()->delete();
  }

  /**
   * Search translations by field value
   * 
   * Performs a search across translation records for a specific field.
   * Uses Eloquent's where clause with LIKE operator for partial matching.
   * 
   * @param string $search - The search term to look for
   * @param string $field - The field to search in (default: 'name')
   * @return Collection - Collection of matching translation models
   */
  public function searchTranslations(string $search, string $field = 'name'): Collection
  {
    return CategoryTranslation::where($field, 'like', "%{$search}%")->get();
  }
}
