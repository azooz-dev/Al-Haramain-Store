<?php

namespace Modules\Catalog\Contracts;

use Modules\Catalog\Entities\Product\Product;

interface ProductTranslationServiceInterface
{
    /**
     * Get translated name for a product
     *
     * @param Product $product
     * @param string|null $locale
     * @return string
     */
    public function getTranslatedName(Product $product, ?string $locale = null): string;

    /**
     * Get form data for a product
     *
     * @param Product $product
     * @return array
     */
    public function getFormData(Product $product): array;

    /**
     * Save translation data for a product
     *
     * @param Product $product
     * @param array $translationData
     * @return void
     */
    public function saveTranslation(Product $product, array $translationData): void;

    /**
     * Generate slug from product name
     *
     * @param string $productName
     * @return string
     */
    public function generateSlugFromName(string $productName): string;
}

