<?php

namespace Modules\Offer\Observers\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Illuminate\Support\Facades\Storage;

class OfferObserver
{
    /**
     * Handle the Offer "creating" event.
     */
    public function creating(Offer $offer): void
    {
        // Initialize products_total_price to 0 if not set
        if (empty($offer->products_total_price)) {
            $offer->products_total_price = 0;
        }
    }

    /**
     * Handle the Offer "updated" event.
     */
    public function updating(Offer $offer): void
    {
        if ($offer->isDirty('image_path')) {
            $originalImagePath = $offer->getOriginal('image_path');
            if ($originalImagePath) {
                Storage::disk('public')->delete($originalImagePath);
            }
        }
        $offer->recalculateTotalPrice();
    }

    /**
     * Handle the Offer "deleted" event.
     */
    public function deleted(Offer $offer): void
    {
        if ($offer->image_path) {
            Storage::disk('public')->delete($offer->image_path);
        }
    }
}
