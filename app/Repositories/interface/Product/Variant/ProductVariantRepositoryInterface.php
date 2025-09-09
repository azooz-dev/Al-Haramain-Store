<?php

namespace App\Repositories\Interface\Product\Variant;

interface ProductVariantRepositoryInterface
{
  public function getStockForVariant(int $variantId): int;

  public function calculateTotalVariant(int $variantId): float;

  public function decrementVariantStock(int $variantId, int $quantity);

  public function getVariantsByIds(array $ids);

  public function fetchAllVariants($productIds, $colorIds, $variantIds);
}
