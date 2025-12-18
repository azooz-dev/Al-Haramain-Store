<?php

namespace Modules\Catalog\Contracts;

interface ProductStockServiceInterface
{
    /**
     * Calculate product quantities from variants and items.
     *
     * @param array $variants Array of variants keyed by variant ID
     * @param array $items Array of variant_id => ['quantity' => int, ...]
     * @return array Array of product_id => quantity
     */
    public function calculateProductQuantitiesFromVariants($variants, array $items): array;

    /**
     * Decrement product stock for the given product quantities.
     *
     * @param array $productQuantities Array of product_id => quantity
     * @return void
     */
    public function decrementProductStock(array $productQuantities): void;
}

