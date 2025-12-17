<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\Catalog\Entities\Product\Product;
use Modules\Offer\Entities\Offer\Offer;

class CalculatePricesStep implements OrderProcessingStep
{
    public function handle(array $data, \Closure $next)
    {
        $variants = $data['_variants'] ?? collect();
        $offers = $data['_offers'] ?? collect();
        
        $newItems = [];
        
        foreach ($data['items'] as $item) {
            if ($item['orderable_type'] === Product::class) {
                $variant = $variants->get($item['variant_id']);
                $item['total_price'] = $variant->effective_price * $item['quantity'];
            } else if ($item['orderable_type'] === Offer::class) {
                $offer = $offers->get($item['orderable_id']);
                $item['total_price'] = $offer->offer_price * $item['quantity'];
            }
            $newItems[] = $item;
        }

        $data['items'] = $newItems;

        return $next($data);
    }
}


