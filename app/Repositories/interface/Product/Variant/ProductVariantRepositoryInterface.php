<?php

namespace App\Repositories\interface\Product\Variant;

interface ProductVariantRepositoryInterface
{
  public function getStockForVariant($variantId): int;

  public function calculateTotalVariant($variantId): float;

  public function decrementVariantStock($variantId, $quantity);

  public function getVariantsByIds(array $ids);

  public function fetchAllVariants($productIds, $colorIds, $variantIds);
}
