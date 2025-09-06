<?php

namespace App\Services\Offer;

use App\Exceptions\Offer\OfferException;
use App\Http\Resources\Offer\OfferApiResource;
use App\Repositories\Interface\Offer\OfferRepositoryInterface;

use function App\Helpers\errorResponse;

class OfferService
{
  public function __construct(private OfferRepositoryInterface $offerRepository) {}

  public function fetchAllOffers()
  {
    try {
      $offers = $this->offerRepository->getAllOffers();

      return OfferApiResource::collection($offers);
    } catch (OfferException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function findOfferById(int $offerId)
  {
    try {
      $offer = $this->offerRepository->findOfferById($offerId);

      return new OfferApiResource($offer);
    } catch (OfferException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }
}
