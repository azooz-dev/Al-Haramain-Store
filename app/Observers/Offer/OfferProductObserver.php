<?php

namespace App\Observers\Offer;

use App\Models\Offer\OfferProduct;

class OfferProductObserver
{
  /**
   * Handle the OfferProduct "created" event.
   */
  public function created(OfferProduct $offerProduct): void
  {
    $this->recalculateOfferTotal($offerProduct);
  }

  /**
   * Handle the OfferProduct "updated" event.
   */
  public function updated(OfferProduct $offerProduct): void
  {
    $this->recalculateOfferTotal($offerProduct);
  }

  /**
   * Handle the OfferProduct "deleted" event.
   */
  public function deleted(OfferProduct $offerProduct): void
  {
    $this->recalculateOfferTotal($offerProduct);
  }

  /**
   * Recalculate the total price for the offer
   */
  private function recalculateOfferTotal(OfferProduct $offerProduct): void
  {
    $offer = $offerProduct->offer;
    if ($offer) {
      $offer->recalculateTotalPrice();
    }
  }
}
