<?php

namespace Modules\Catalog\Contracts;

use Modules\Catalog\Entities\Category\Category;

interface CategoryTranslationServiceInterface
{
    /**
     * Get translated name for a category
     *
     * @param Category $category
     * @param string|null $locale
     * @return string
     */
    public function getTranslatedName(Category $category, ?string $locale = null): string;

    /**
     * Get form data for a category
     *
     * @param Category $category
     * @return array
     */
    public function getFormData(Category $category): array;

    /**
     * Save translation data for a category
     *
     * @param Category $category
     * @param array $translationData
     * @return void
     */
    public function saveTranslations(Category $category, array $translationData): void;

    /**
     * Generate slug from category name
     *
     * @param string $categoryName
     * @return string
     */
    public function generateSlugFromName(string $categoryName): string;
}

