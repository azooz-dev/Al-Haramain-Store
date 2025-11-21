<?php

namespace App\Repositories\Interface\Offer;

use App\Models\Offer\Offer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface OfferRepositoryInterface
{
  public function getAllOffers(): Collection;

  public function findOfferById(int $offerId);

  public function getOfferProducts(int $offerId);

  public function findOffersByIds(array $offerIds);

  public function create(array $data): Offer;

  public function update(int $id, array $data): Offer;

  public function delete(int $id): bool;

  public function count(): int;

  public function getQueryBuilder(): Builder;
}
