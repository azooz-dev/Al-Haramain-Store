<?php

namespace App\Repositories\Interface\Category;

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
 * @package App\Repositories\Interface\Category
 */
interface CategoryRepositoryInterface
{
  public function getAllCategories(): ?Collection;

  public function findById(int $id): ?Category;

  public function findByIdWithTranslations(int $id): ?Category;

  public function searchByName(string $search): Collection;

  public function slugExists(string $slug): bool;
}
