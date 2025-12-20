<?php

namespace Modules\Catalog\Contracts;

use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Eloquent\Builder;

interface ProductServiceInterface
{
    /**
     * Get product by ID
     */
    public function getProductById(int $id);

    /**
     * Find product by ID and return as API resource
     */
    public function findProductById(int $id);

    /**
     * Check if color belongs to product
     */
    public function checkColorBelongsToProduct(int $productId, int $colorId);

    /**
     * Check if variant belongs to product and color
     */
    public function checkVariantBelongsToProductAndColor(int $productId, int $colorId, int $variantId);

    /**
     * Create a new product
     */
    public function createProduct(array $data, array $translationData, ?array $categoryIds = null): Product;

    /**
     * Update a product
     */
    public function updateProduct(int $id, array $data, array $translationData, ?array $categoryIds = null): Product;

    /**
     * Delete a product
     */
    public function deleteProduct(int $id): bool;

    /**
     * Get total products count
     */
    public function getProductsCount(): int;

    /**
     * Get query builder for custom queries
     */
    public function getQueryBuilder(): Builder;

    /**
     * Get price range for a product
     */
    public function getPriceRange(Product $product): string;
}

