<?php

namespace App\Services\Order\Pipeline;

use App\Services\Product\Variant\ProductVariantService;
use App\Services\Product\ProductStockService;

class UpdateStockStep implements OrderProcessingStep
{
    public function __construct(
        private ProductVariantService $variantService,
        private ProductStockService $productStockService
    ) {}

    public function handle(array $data, \Closure $next)
    {
        $groupedItems = $data['_grouped_items'];
        $offers = $data['_offers'] ?? collect();

        if ($offers->isNotEmpty()) {
            $offerProducts = $this->buildOfferProductsForStockUpdate($offers);
            $allItems = ($groupedItems[\App\Models\Product\Product::class] ?? []) + $offerProducts;
        } else {
            $allItems = $groupedItems[\App\Models\Product\Product::class] ?? [];
        }

        if (empty($allItems)) {
            return $next($data);
        }

        $variantIds = array_keys($allItems);
        $variants = $this->variantService->getVariantsByIds($variantIds);

        $this->variantService->decrementVariantStock($allItems);

        $productQuantities = $this->productStockService->calculateProductQuantitiesFromVariants(
            $variants, 
            $allItems
        );

        $this->productStockService->decrementProductStock($productQuantities);

        return $next($data);
    }

    private function buildOfferProductsForStockUpdate($offers): array
    {
        return $offers->flatMap(function ($offer) {
            return $offer->offerProducts;
        })->mapWithKeys(function ($offerProduct) {
            return [
                $offerProduct->product_variant_id => [
                    'quantity' => $offerProduct->quantity,
                    'variant_id' => $offerProduct->product_variant_id,
                    'color_id' => $offerProduct->product_color_id,
                    'orderable_type' => 'offer',
                    'orderable_id' => $offerProduct->offer_id,
                ]
            ];
        })->toArray();
    }
}

