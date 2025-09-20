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
      $offer = $this->retrieveOfferById($offerId);

      return new OfferApiResource($offer);
    } catch (OfferException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function retrieveOfferById(int $offerId)
  {
    $offer = $this->offerRepository->findOfferById($offerId);

    return $offer;
  }

  public function getOfferProductForOrder(int $offerId)
  {
    $offerProducts = $this->offerRepository->getOfferProducts($offerId);

    foreach ($offerProducts as $offerProduct) {
      $offerProduct['variant_id'] = $offerProduct->product_variant_id;
      $offerProduct['color_id'] = $offerProduct->product_color_id;
      $offerProduct['orderable_type'] = 'offer';
      $offerProduct['orderable_id'] = $offerProduct->product_id;
      $offerProduct['price'] = $offerProduct->offer->offer_price;
    }

    return $offerProducts->toArray();
  }
}
