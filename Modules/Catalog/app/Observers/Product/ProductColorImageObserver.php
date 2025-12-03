<?php

namespace Modules\Catalog\Observers\Product;

use Illuminate\Support\Facades\Storage;
use Modules\Catalog\Entities\Product\ProductColorImage;

class ProductColorImageObserver
{

    /**
     * Handle the ProductColorImage "updated" event.
     */
    public function updating(ProductColorImage $productColorImage): void
    {
        if ($productColorImage->isDirty('image_url')) {
            $originalImagePath = $productColorImage->getOriginal('image_url');
            if ($originalImagePath) {
                Storage::disk('public')->delete($originalImagePath);
            }
        }
    }

    /**
     * Handle the ProductColorImage "deleted" event.
     */
    public function deleted(ProductColorImage $productColorImage): void
    {
        if ($productColorImage->image_url) {
            Storage::disk('public')->delete($productColorImage->image_url);
        }
    }
}


