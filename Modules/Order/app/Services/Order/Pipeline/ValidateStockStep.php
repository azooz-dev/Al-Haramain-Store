<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\Catalog\Entities\Product\Product;
use Modules\Offer\Entities\Offer\Offer;
use Modules\Catalog\Contracts\ProductVariantServiceInterface;
use Modules\Offer\Services\Offer\OfferService;

class ValidateStockStep implements OrderProcessingStep
{
    public function __construct(
        private ProductVariantServiceInterface $variantService,
        private OfferService $offerService
    ) {}

    public function handle(array $data, \Closure $next)
    {
        $groupedItems = $this->groupOrderItemsByTypeAndId($data['items']);

        // Validate product stock
        if (isset($groupedItems[Product::class])) {
            $variantIds = array_keys($groupedItems[Product::class]);
            $variants = $this->variantService->getVariantsByIds($variantIds);
            $this->variantService->checkStock($groupedItems[Product::class]);

            // Attach variants to data for later use
            $data['_variants'] = $variants;
        }

        // Validate offer stock
        if (isset($groupedItems[Offer::class])) {
            $offerIds = array_keys($groupedItems[Offer::class]);
            $offers = $this->offerService->getOffersByIds($offerIds)->keyBy('id');

            // Build offer variants for validation
            $offerVariants = $this->buildOfferVariantsForValidation($offers, $groupedItems[Offer::class]);
            if (!empty($offerVariants)) {
                $this->variantService->checkStock($offerVariants);
            }

            // Attach offers to data for later use
            $data['_offers'] = $offers;
        }

        $data['_grouped_items'] = $groupedItems;

        return $next($data);
    }

    private function groupOrderItemsByTypeAndId(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            if (!isset($item['orderable_type'])) {
                continue;
            }

            $type = $item['orderable_type'];

            if ($type === Product::class) {
                if (!isset($item['variant_id'])) {
                    continue;
                }
                $id = $item['variant_id'];
            } else {
                if (!isset($item['orderable_id'])) {
                    continue;
                }
                $id = $item['orderable_id'];
            }

            $grouped[$type][$id] = $item;
        }
        return $grouped;
    }

    private function buildOfferVariantsForValidation($offers, array $offerItems): array
    {
        $offerVariants = [];

        foreach ($offerItems as $offerId => $offerItem) {
            $offer = $offers->get($offerId);
            if (!$offer) {
                continue;
            }

            foreach ($offer->offerProducts as $offerProduct) {
                $variantId = $offerProduct->product_variant_id;
                if (!isset($offerVariants[$variantId])) {
                    $offerVariants[$variantId] = [
                        'quantity' => $offerProduct->quantity * $offerItem['quantity'],
                        'variant_id' => $variantId,
                        'color_id' => $offerProduct->product_color_id,
                    ];
                } else {
                    $offerVariants[$variantId]['quantity'] += $offerProduct->quantity * $offerItem['quantity'];
                }
            }
        }

        return $offerVariants;
    }
}
