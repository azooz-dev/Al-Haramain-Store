<?php

namespace App\Services\Product\Variant;

use App\Exceptions\Product\Variant\OutOfStockException;
use App\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;

class ProductVariantService
{
  public function __construct(private ProductVariantRepositoryInterface $productVariantRepository) {}

  public function checkStock($items)
  {
    $variants = $this->getVariantsByIds(array_keys($items));

    foreach ($items as $variantId => $item) {
      $variant = $variants[$variantId] ?? null;
      if (!$variant || $variant->quantity < $item['quantity']) {
        throw new OutOfStockException(__('app.messages.order.validation.variant_quantity_exceeds_stock', ['variant_quantity' => $item['quantity'], 'total_stock' => $variant->quantity]));
      }
    }
  }

  public function getVariantsByIds($ids)
  {
    return $this->productVariantRepository->getVariantsByIds($ids);
  }



  public function calculateTotalOrderPrice($items)
  {
    $itemTotal = 0;
    foreach ($items as $item) {
      if ($item['orderable_type'] === 'offer') {
        $itemTotal += $item['total_price'];
      } else {
        $itemTotal += $this->calculateTotalVariantPrice($item['variant_id'], $item['quantity']);
      }
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
    foreach ($items as $itemId => $item) {
      $this->productVariantRepository->decrementVariantStock($itemId, $item['quantity']);
    }
  }

  public function fetchAllVariants($productIds, $colorIds, $variantIds)
  {
    return $this->productVariantRepository->fetchAllVariants($productIds, $colorIds, $variantIds);
  }
}
