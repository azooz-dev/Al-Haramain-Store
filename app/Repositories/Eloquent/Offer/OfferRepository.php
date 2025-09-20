<?php


namespace App\Repositories\Eloquent\Offer;

use App\Models\Offer\Offer;
use App\Models\Offer\OfferProduct;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interface\Offer\OfferRepositoryInterface;

class OfferRepository implements OfferRepositoryInterface
{
  public function getAllOffers(): Collection
  {
    return Offer::with(['translations', 'products'])->get();
  }

  public function findOfferById(int $offerId)
  {
    return Offer::with(['translations', 'products'])->find($offerId);
  }

  public function getOfferProducts(int $offerId)
  {
    return OfferProduct::where("offer_id", $offerId)->get();
  }
}
