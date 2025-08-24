<?php

namespace App\Repositories\Eloquent\Category;

use App\Models\Category\Category;
use App\Repositories\interface\Category\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * EloquentCategoryRepository
 * 
 * Eloquent implementation of CategoryRepositoryInterface.
 * Provides concrete database operations using Laravel's Eloquent ORM.
 * 
 * This implementation handles:
 * - Direct database queries using Eloquent models
 * - Eager loading of relationships to avoid N+1 queries
 * - Complex queries with joins and where clauses
 * - Slug validation and uniqueness checks
 * 
 * @package App\Repositories\Eloquent\Category
 */
class CategoryRepository implements CategoryRepositoryInterface
{
  /**
   * Find a category by its ID
   * 
   * Simple lookup by primary key. Does not load translations.
   * Use findByIdWithTranslations() if you need translations loaded.
   * 
   * @param int $id - The category ID to find
   * @return Category|null - The category model or null if not found
   */
  public function findById(int $id): ?Category
  {
    return Category::find($id);
  }

  /**
   * Find a category by ID with translations loaded
   * 
   * Uses eager loading to load translations in a single query,
   * preventing N+1 query problems when accessing translations.
   * 
   * @param int $id - The category ID to find
   * @return Category|null - The category with translations loaded or null if not found
   */
  public function findByIdWithTranslations(int $id): ?Category
  {
    return Category::with('translations')->find($id);
  }

  /**
   * Get all categories with translations loaded
   * 
   * Retrieves all categories and eagerly loads their translations.
   * This is more efficient than loading translations individually.
   * 
   * @return Collection - Collection of categories with translations loaded
   */
  public function getAllWithTranslations(): Collection
  {
    return Category::with('translations')->get();
  }

  /**
   * Create a new category
   * 
   * Creates a new category record using Eloquent's create method.
   * Only handles main category data - translations are handled separately.
   * 
   * @param array $data - Category data (slug, image, etc.)
   * @return Category - The newly created category model
   */
  public function create(array $data): Category
  {
    return Category::create($data);
  }

  /**
   * Update an existing category
   * 
   * Updates an existing category record using Eloquent's update method.
   * Only handles main category data - translations are handled separately.
   * 
   * @param Category $category - The category model to update
   * @param array $data - Updated category data
   * @return bool - True if update was successful, false otherwise
   */
  public function update(Category $category, array $data): bool
  {
    return $category->update($data);
  }

  /**
   * Delete a category
   * 
   * Removes a category from the database. Eloquent will handle
   * related translations deletion if foreign key constraints are set up.
   * 
   * @param Category $category - The category model to delete
   * @return bool - True if deletion was successful, false otherwise
   */
  public function delete(Category $category): bool
  {
    return $category->delete();
  }

  /**
   * Search categories by name across translations
   * 
   * Performs a complex query that searches across all translation names
   * and returns categories that match the search term. Uses whereHas
   * to search in related translation records.
   * 
   * @param string $search - The search term to look for
   * @return Collection - Collection of matching categories with translations loaded
   */
  public function searchByName(string $search): Collection
  {
    return Category::whereHas('translations', function ($query) use ($search) {
      $query->where('name', 'like', "%{$search}%");
    })->with('translations')->get();
  }

  /**
   * Check if a slug already exists
   * 
   * Simple existence check using Eloquent's exists() method.
   * Used for slug generation to ensure uniqueness.
   * 
   * @param string $slug - The slug to check
   * @return bool - True if slug exists, false otherwise
   */
  public function slugExists(string $slug): bool
  {
    return Category::where('slug', $slug)->exists();
  }
}
