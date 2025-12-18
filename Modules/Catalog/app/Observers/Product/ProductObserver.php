<?php

namespace Modules\Catalog\Observers\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Events\ProductUpdated;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Dispatch ProductUpdated event when product quantity changes
        // Analytics module will listen and invalidate cache
        if ($product->isDirty('quantity')) {
            ProductUpdated::dispatch($product);
        }
    }
}


