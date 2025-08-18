<?php

namespace App\Services;

use App\Models\Category\Category;
use App\Repositories\interface\Category\CategoryRepositoryInterface;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * CategoryTranslationService
 * 
 * This service handles all business logic related to category translations and slug generation.
 * It follows the Repository-Service pattern and provides a clean interface for:
 * - Managing category translations (English/Arabic)
 * - Generating unique slugs from translation names
 * - Retrieving translated content for display
 * - Handling form data for Filament admin panel
 * 
 * @package App\Services
 */
class CategoryTranslationService
{
  /**
   * Constructor - Dependency Injection
   * 
   * Injects the repository interfaces to maintain loose coupling
   * and enable easy testing with mock repositories.
   * 
   * @param CategoryRepositoryInterface $categoryRepository - Handles category data operations
   * @param CategoryTranslationRepositoryInterface $translationRepository - Handles translation data operations
   */
  public function __construct(
    private CategoryRepositoryInterface $categoryRepository,
    private CategoryTranslationRepositoryInterface $translationRepository
  ) {}

  /**
   * Get all translations for a specific category
   * 
   * Retrieves all translation records (English, Arabic, etc.) for a given category.
   * Useful for bulk operations or when you need all translations at once.
   * 
   * @param Category $category - The category to get translations for
   * @return Collection - Collection of CategoryTranslation models
   */
  public function getTranslationsForCategory(Category $category): Collection
  {
    return $this->translationRepository->getTranslationsForCategory($category);
  }

  /**
   * Get the translated name for a category in a specific locale
   * 
   * Retrieves the name translation for the given locale. If the requested locale
   * doesn't exist, it falls back to English as the default language.
   * 
   * @param Category $category - The category to get the name for
   * @param string|null $locale - The locale to get (e.g., 'en', 'ar'). If null, uses app locale
   * @return string - The translated name or empty string if not found
   */
  public function getTranslatedName(Category $category, ?string $locale = null): string
  {
    // Use app locale if none specified
    $locale = $locale ?: app()->getLocale();

    // Try to get translation for the requested locale
    $translation = $this->translationRepository->getTranslationByLocale($category, $locale);

    // Fallback to English if requested locale doesn't exist
    if (!$translation) {
      $translation = $this->translationRepository->getTranslationByLocale($category, 'en');
    }

    return $translation?->name ?? '';
  }

  /**
   * Get the translated description for a category in a specific locale
   * 
   * Similar to getTranslatedName() but for descriptions. Falls back to English
   * if the requested locale doesn't exist.
   * 
   * @param Category $category - The category to get the description for
   * @param string|null $locale - The locale to get (e.g., 'en', 'ar'). If null, uses app locale
   * @return string - The translated description or empty string if not found
   */
  public function getTranslatedDescription(Category $category, ?string $locale = null): string
  {
    // Use app locale if none specified
    $locale = $locale ?: app()->getLocale();

    // Try to get translation for the requested locale
    $translation = $this->translationRepository->getTranslationByLocale($category, $locale);

    // Fallback to English if requested locale doesn't exist
    if (!$translation) {
      $translation = $this->translationRepository->getTranslationByLocale($category, 'en');
    }

    return $translation?->description ?? '';
  }

  /**
   * Save or update translations for a category
   * 
   * Processes translation data and saves/updates records for both English and Arabic.
   * Only saves translations that have at least a name or description.
   * 
   * @param Category $category - The category to save translations for
   * @param array $translationData - Array with 'en' and 'ar' keys containing name/description
   * @return void
   */
  public function saveTranslations(Category $category, array $translationData): void
  {
    // Process each supported locale
    foreach (['en', 'ar'] as $locale) {
      $payload = $translationData[$locale] ?? [];

      // Only save if we have meaningful data (name or description)
      if (!empty($payload['name']) || !empty($payload['description'])) {
        $this->translationRepository->updateOrCreateTranslation($category, $locale, $payload);
      }
    }
  }

  /**
   * Get formatted data for Filament form population
   * 
   * Retrieves all necessary data (slug, image, translations) in the format
   * expected by Filament forms. Used for editing existing categories.
   * 
   * @param Category $category - The category to get form data for
   * @return array - Formatted data with 'slug', 'image', 'en', and 'ar' keys
   */
  public function getFormData(Category $category): array
  {
    // Get existing translations for both locales
    $enTranslation = $this->translationRepository->getTranslationByLocale($category, 'en');
    $arTranslation = $this->translationRepository->getTranslationByLocale($category, 'ar');

    return [
      'slug' => $category->slug,
      'image' => $category->image,
      'en' => [
        'name' => $enTranslation?->name ?? '',
        'description' => $enTranslation?->description ?? '',
      ],
      'ar' => [
        'name' => $arTranslation?->name ?? '',
        'description' => $arTranslation?->description ?? '',
      ],
    ];
  }

  /**
   * Find a category with its translations loaded
   * 
   * Retrieves a category and eagerly loads its translations to avoid N+1 queries.
   * Useful when you need both category data and translations in one query.
   * 
   * @param int $id - The category ID to find
   * @return Category|null - The category with translations or null if not found
   */
  public function findCategoryWithTranslations(int $id): ?Category
  {
    return $this->categoryRepository->findByIdWithTranslations($id);
  }

  /**
   * Search categories by name across all translations
   * 
   * Performs a search across all translation names (English, Arabic, etc.)
   * and returns categories that match the search term.
   * 
   * @param string $search - The search term to look for
   * @return Collection - Collection of matching categories with translations
   */
  public function searchCategoriesByName(string $search): Collection
  {
    return $this->categoryRepository->searchByName($search);
  }

  /**
   * Generate a unique slug from translation data
   * 
   * Creates a URL-friendly slug from the English name (or Arabic as fallback).
   * Ensures uniqueness by checking existing slugs and adding numbers if needed.
   * 
   * @param array $translationData - Array with 'en' and 'ar' keys containing name/description
   * @return string - A unique, URL-friendly slug
   * @throws \InvalidArgumentException - If no valid name is found in translations
   */
  public function generateSlugFromTranslations(array $translationData): string
  {
    // Priority: English name first, then Arabic as fallback
    $name = $translationData['en']['name'] ?? $translationData['ar']['name'] ?? null;

    if (empty($name)) {
      throw new \InvalidArgumentException('No valid name found in translations for slug generation.');
    }

    return $this->generateUniqueSlug($name);
  }

  /**
   * Generate a unique slug from a name string
   * 
   * Converts a name to a URL-friendly slug and ensures it's unique
   * by adding numbers if the slug already exists.
   * 
   * @param string $name - The name to convert to a slug
   * @return string - A unique, URL-friendly slug
   */
  public function generateUniqueSlug(string $name): string
  {
    // Convert name to URL-friendly format (lowercase, hyphens, no special chars)
    $baseSlug = \Illuminate\Support\Str::slug($name, '-');
    $slug = $baseSlug;
    $counter = 1;

    // Keep trying with incremented numbers until we find a unique slug
    while ($this->slugExists($slug)) {
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }

    return $slug;
  }

  /**
   * Check if a slug already exists in the database
   * 
   * Used internally by generateUniqueSlug() to ensure slug uniqueness.
   * 
   * @param string $slug - The slug to check
   * @return bool - True if slug exists, false otherwise
   */
  private function slugExists(string $slug): bool
  {
    return $this->categoryRepository->slugExists($slug);
  }

  /**
   * Generate slug for existing category (for updates)
   * 
   * Similar to generateUniqueSlug() but excludes the current category
   * from uniqueness checks. Used when updating an existing category.
   * 
   * @param string $name - The name to convert to a slug
   * @param int $categoryId - The ID of the category being updated (to exclude from checks)
   * @return string - A unique, URL-friendly slug
   */
  public function generateSlugForUpdate(string $name, int $categoryId): string
  {
    // Convert name to URL-friendly format
    $baseSlug = \Illuminate\Support\Str::slug($name, '-');
    $slug = $baseSlug;
    $counter = 1;

    // Keep trying with incremented numbers until we find a unique slug
    // (excluding the current category from uniqueness checks)
    while ($this->slugExistsExcluding($slug, $categoryId)) {
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }

    return $slug;
  }

  /**
   * Check if a slug exists excluding a specific category ID
   * 
   * Used internally by generateSlugForUpdate() to ensure slug uniqueness
   * while excluding the current category from the check.
   * 
   * @param string $slug - The slug to check
   * @param int $excludeId - The category ID to exclude from the check
   * @return bool - True if slug exists (excluding the specified ID), false otherwise
   */
  private function slugExistsExcluding(string $slug, int $excludeId): bool
  {
    return $this->categoryRepository->slugExistsExcluding($slug, $excludeId);
  }
}
