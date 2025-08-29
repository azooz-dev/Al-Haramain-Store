<?php

namespace App\Observers\Offer;

use App\Models\Offer\Offer;
use Illuminate\Support\Facades\Storage;

class OfferObserver
{

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
