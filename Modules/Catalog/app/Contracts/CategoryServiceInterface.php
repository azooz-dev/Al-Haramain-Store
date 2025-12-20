<?php

namespace Modules\Catalog\Contracts;

use Modules\Catalog\Entities\Category\Category;
use Illuminate\Database\Eloquent\Builder;

interface CategoryServiceInterface
{
    /**
     * Get all categories
     */
    public function getCategories();

    /**
     * Find category by ID and return as API resource
     */
    public function findCategoryById(int $id);

    /**
     * Create a new category
     */
    public function createCategory(array $data, array $translationData): Category;

    /**
     * Update a category
     */
    public function updateCategory(int $id, array $data, array $translationData): Category;

    /**
     * Delete a category
     */
    public function deleteCategory(int $id): bool;

    /**
     * Get total categories count
     */
    public function getCategoriesCount(): int;

    /**
     * Get query builder for custom queries
     */
    public function getQueryBuilder(): Builder;

    /**
     * Get product count for a category
     */
    public function getProductCount(Category $category): int;
}

