<?php

namespace App\Repositories\Eloquent\Product\Variant;

use App\Models\Product\ProductVariant;
use App\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;

class ProductVariantRepository implements ProductVariantRepositoryInterface
{
  public function getStockForVariant(int $variantId): int
  {
    return ProductVariant::findOrFail($variantId)->quantity;
  }

  public function calculateTotalVariant(int $variantId): float
  {
    $variant = ProductVariant::findOrFail($variantId);

    return (float) ($variant->amount_discount_price ?? $variant->price);
  }

  public function decrementVariantStock(int $variantId, int $quantity)
  {
    return ProductVariant::where('id', $variantId)
      ->where('quantity', '>=', $quantity)
      ->decrement('quantity', $quantity);
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

  public function variantBelongsToProductAndColor(int $productId, int $colorId, int $variantId): bool
  {
    return ProductVariant::where('product_id', $productId)->where('color_id', $colorId)->where('id', $variantId)->exists();
  }
}
