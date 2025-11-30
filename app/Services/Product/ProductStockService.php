<?php

namespace App\Services\Product;

use App\Repositories\Interface\Product\ProductRepositoryInterface;

class ProductStockService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function decrementProductStock(array $productQuantities): void
    {
        foreach ($productQuantities as $productId => $quantity) {
            $this->productRepository->decrementProductStock($productId, $quantity);
        }
    }

    public function calculateProductQuantitiesFromVariants($variants, array $items): array
    {
        $productQuantities = [];

        foreach ($items as $variantId => $item) {
            $variant = $variants[$variantId] ?? null;
            
            if (!$variant) {
                continue;
            }

            $productId = $variant->product_id;
            if (!isset($productQuantities[$productId])) {
                $productQuantities[$productId] = 0;
            }
            $productQuantities[$productId] += $item['quantity'];
        }

        return $productQuantities;
    }
}

