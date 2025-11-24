<?php

namespace App\Repositories\Interface\Offer;

use App\Models\Offer\Offer;
use Illuminate\Database\Eloquent\Collection;

interface ReadOfferRepositoryInterface
{
    public function getAllOffers(): Collection;

    public function findOfferById(int $offerId): ?Offer;

    public function findOffersByIds(array $offerIds): Collection;

    public function getOfferProducts(int $offerId): Collection;

    public function count(): int;
}


