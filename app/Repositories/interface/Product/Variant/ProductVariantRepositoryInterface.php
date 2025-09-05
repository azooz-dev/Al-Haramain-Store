<?php

namespace App\Repositories\interface\Product\Variant;

interface ProductVariantRepositoryInterface
{
  public function getStockForVariant($variantId): int;

  public function calculateTotalVariant($variantId): int;

  public function decrementVariantStock($variantId, $quantity);

  public function getVariantsByIds(array $ids);
}
