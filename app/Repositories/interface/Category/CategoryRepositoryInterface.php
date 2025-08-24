<?php

namespace App\Repositories\interface\Category;

use App\Models\Category\Category;
use Illuminate\Support\Collection;

/**
 * CategoryRepositoryInterface
 * 
 * This interface defines the contract for category data access operations.
 * It abstracts the database layer and provides a clean interface for category-related queries.
 * 
 * Key Responsibilities:
 * - Basic CRUD operations for categories
 * - Finding categories with their translations
 * - Searching categories by name across translations
 * - Slug validation and uniqueness checks
 * 
 * Implementation:
 * - EloquentCategoryRepository provides the concrete implementation
 * - Can be easily swapped for testing or different data sources
 * 
 * @package App\Repositories\interface\Category
 */
interface CategoryRepositoryInterface
{
  /**
   * Find a category by its ID
   * 
   * Retrieves a single category record from the database.
   * Does not load translations - use findByIdWithTranslations() if you need translations.
   * 
   * @param int $id - The category ID to find
   * @return Category|null - The category model or null if not found
   */
  public function findById(int $id): ?Category;

  /**
   * Find a category by ID with translations loaded
   * 
   * Retrieves a category and eagerly loads its translations to avoid N+1 queries.
   * Use this when you need both category data and translations in one query.
   * 
   * @param int $id - The category ID to find
   * @return Category|null - The category with translations loaded or null if not found
   */
  public function findByIdWithTranslations(int $id): ?Category;

  /**
   * Get all categories with translations loaded
   * 
   * Retrieves all categories and eagerly loads their translations.
   * Useful for listing pages where you need to display translated names.
   * 
   * @return Collection - Collection of categories with translations loaded
   */
  public function getAllWithTranslations(): Collection;

  /**
   * Search categories by name across translations
   * 
   * Performs a search across all translation names (English, Arabic, etc.)
   * and returns categories that match the search term.
   * 
   * @param string $search - The search term to look for
   * @return Collection - Collection of matching categories with translations loaded
   */
  public function searchByName(string $search): Collection;

  /**
   * Check if a slug already exists
   * 
   * Validates slug uniqueness by checking if it already exists in the database.
   * Used for slug generation to ensure no duplicates.
   * 
   * @param string $slug - The slug to check
   * @return bool - True if slug exists, false otherwise
   */
  public function slugExists(string $slug): bool;
}
