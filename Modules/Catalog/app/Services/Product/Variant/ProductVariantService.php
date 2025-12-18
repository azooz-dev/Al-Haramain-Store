<?php

namespace Modules\Catalog\Services\Product\Variant;

use Modules\Offer\Entities\Offer\Offer;
use Modules\Catalog\Contracts\ProductVariantServiceInterface;
use Modules\Catalog\Exceptions\Product\Variant\OutOfStockException;
use Modules\Catalog\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;

class ProductVariantService implements ProductVariantServiceInterface
{
  public function __construct(private ProductVariantRepositoryInterface $productVariantRepository) {}

  public function checkStock($items)
  {
    $variants = $this->getVariantsByIds(array_keys($items));

    foreach ($items as $variantId => $item) {
      $variant = $variants[$variantId] ?? null;
      if (!$variant || $variant->quantity < $item['quantity']) {
        throw new OutOfStockException(__('app.messages.order.validation.variant_quantity_exceeds_stock', ['variant_quantity' => $item['quantity'], 'total_stock' => $variant->quantity]), 422);
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
      if ($item['orderable_type'] === Offer::class) {
        $itemTotal += $item['total_price'];
      } else {
        // Only calculate variant price if variant_id is not null
        if ($item['variant_id'] !== null) {
          $itemTotal += $this->calculateTotalVariantPrice($item['variant_id'], $item['quantity']);
        } else {
          // If variant_id is null, use the total_price that was already calculated
          $itemTotal += $item['total_price'];
        }
      }
    }

    return $itemTotal;
  }

  public function calculateTotalVariantPrice($variantId, $quantity): float
  {
    if ($variantId === null) {
      return 0.0;
    }

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
