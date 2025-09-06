<?php

namespace App\Services\Product\Variant;

use App\Exceptions\Product\Variant\OutOfStockException;
use App\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;

class ProductVariantService
{
  public function __construct(private ProductVariantRepositoryInterface $productVariantRepository) {}

  public function checkStock($items)
  {
    $variants = $this->productVariantRepository->getVariantsByIds(array_keys($items));

    foreach ($items as $item) {
      $variant = $variants['variant_id'] ?? null;
      if (!$variant || $variant->quantity < $item['variant_id']) {
        throw new OutOfStockException(__('app.messages.order.validation.variant_quantity_exceeds_stock', ['variant_quantity' => $item['quantity'], 'total_stock' => $variant->quantity]));
      }
    }
  }



  public function calculateTotalOrderPrice($items)
  {
    $itemTotal = 0;
    foreach ($items as $item) {
      $itemTotal += $this->calculateTotalVariantPrice($item['variant_id'], $item['quantity']);
    }

    return $itemTotal;
  }

  public function calculateTotalVariantPrice($variantId, $quantity): float
  {
    $price = $this->productVariantRepository->calculateTotalVariant($variantId);

    return $price * $quantity;
  }

  public function decrementVariantStock($items)
  {
    foreach ($items as $item) {
      return $this->productVariantRepository->decrementVariantStock($item['variant_id'], $item['quantity']);
    }
  }

  public function fetchAllVariants($productIds, $colorIds, $variantIds)
  {
    return $this->productVariantRepository->fetchAllVariants($productIds, $colorIds, $variantIds);
  }
}
