<?php

namespace Modules\Catalog\Contracts;

interface ProductVariantServiceInterface
{
    /**
     * Check if stock is available for the given items.
     *
     * @param array $items Array of variant_id => ['quantity' => int, ...]
     * @return void
     * @throws \Modules\Catalog\Exceptions\Product\Variant\OutOfStockException
     */
    public function checkStock($items): void;

    /**
     * Get variants by their IDs.
     *
     * @param array $ids Array of variant IDs
     * @return \Illuminate\Support\Collection Collection of variants keyed by variant ID
     */
    public function getVariantsByIds($ids);

    /**
     * Calculate total order price from items.
     *
     * @param array $items Array of order items
     * @return float Total price
     */
    public function calculateTotalOrderPrice($items): float;

    /**
     * Decrement stock for the given variant items.
     *
     * @param array $items Array of variant_id => ['quantity' => int, ...]
     * @return void
     */
    public function decrementVariantStock($items): void;
}

