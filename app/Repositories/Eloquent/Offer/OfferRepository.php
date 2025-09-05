<?php


namespace App\Repositories\Eloquent\Offer;

use App\Models\Offer\Offer;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\interface\Offer\OfferRepositoryInterface;

class OfferRepository implements OfferRepositoryInterface
{
  public function getAllOffers(): Collection
  {
    return Offer::with(['translations', 'product'])->get();
  }

  public function findOfferById(int $offerId)
  {
    return Offer::with(['translations', 'product'])->find($offerId);
  }
}
