<?php

namespace App\Repositories\Eloquent\Product\Variant;

use App\Models\Product\ProductVariant;
use App\Repositories\interface\Product\Variant\ProductVariantRepositoryInterface;

class ProductVariantRepository implements ProductVariantRepositoryInterface
{
  public function getStockForVariant($variantId): int
  {
    return ProductVariant::find($variantId)->quantity;
  }

  public function calculateTotalVariant($variantId): float
  {
    $variant = ProductVariant::find($variantId);
    $price = $variant->amount_discount_price ?? $variant->price;

    return $price;
  }

  public function decrementVariantStock($variantId, $quantity)
  {
    $variant = ProductVariant::find($variantId);

    $variant->quantity -= $quantity;

    $variant->save();
  }

  public function getVariantsByIds(array $ids)
  {
    return ProductVariant::whereIn('id', $ids)->get()->keyBy('id');
  }

  public function fetchAllVariants($productIds, $colorIds, $variantIds)
  {
    return ProductVariant::whereIn('product_id', $productIds)
      ->whereIn('color_id', $colorIds)
      ->whereIn('id', $variantIds)
      ->get();
  }
}
