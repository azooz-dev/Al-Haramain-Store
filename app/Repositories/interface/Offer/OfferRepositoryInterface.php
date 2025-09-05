<?php

namespace App\Repositories\interface\Offer;

use App\Models\Offer\Offer;
use Illuminate\Database\Eloquent\Collection;

interface OfferRepositoryInterface
{
  public function getAllOffers(): Collection;

  public function findOfferById(int $offerId);
}
