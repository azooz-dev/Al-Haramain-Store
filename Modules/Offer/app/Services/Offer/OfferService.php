<?php

namespace Modules\Offer\Services\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Illuminate\Database\Eloquent\Builder;
use Modules\Offer\Contracts\OfferServiceInterface;
use Modules\Offer\Exceptions\Offer\OfferException;
use Modules\Offer\Http\Resources\Offer\OfferApiResource;
use Modules\Offer\Repositories\Interface\Offer\OfferRepositoryInterface;

use function App\Helpers\errorResponse;

class OfferService implements OfferServiceInterface
{
  public function __construct(
    private OfferRepositoryInterface $offerRepository,
    private OfferTranslationService $translationService
  ) {}

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

  public function getOffersByIds(array $offerIds)
  {
    return $this->offerRepository->findOffersByIds($offerIds);
  }

  public function retrieveOfferById(int $offerId)
  {
    return $this->offerRepository->findOfferById($offerId);
  }

  public function createOffer(array $data, array $translationData): Offer
  {
    // Create offer via repository
    $offer = $this->offerRepository->create($data);

    // Save translations via OfferTranslationService
    $this->translationService->saveTranslation($offer, $translationData);

    // Return offer with relationships loaded
    return $offer->fresh([
      'translations',
      'offerProducts.product.translations',
      'offerProducts.productVariant',
      'offerProducts.productColor',
    ]);
  }

  public function updateOffer(int $id, array $data, array $translationData): Offer
  {
    // Update offer via repository
    $offer = $this->offerRepository->update($id, $data);

    // Update translations via OfferTranslationService
    $this->translationService->saveTranslation($offer, $translationData);

    // Return updated offer with relationships loaded
    return $offer;
  }

  public function deleteOffer(int $id): bool
  {
    return $this->offerRepository->delete($id);
  }

  public function getOffersCount(): int
  {
    return $this->offerRepository->count();
  }

  public function getQueryBuilder(): Builder
  {
    return $this->offerRepository->getQueryBuilder();
  }

  public function getProductsCount(Offer $offer): int
  {
    // Use withCount if available, otherwise use relationship count
    if (isset($offer->offer_products_count)) {
      return (int) $offer->offer_products_count;
    }

    return $offer->offerProducts()->count();
  }

  public function getDiscountAmount(Offer $offer): string
  {
    $discount = $offer->products_total_price - $offer->offer_price;
    return '$' . number_format(max($discount, 0), 2);
  }

  public function getTranslatedName(Offer $offer): string
  {
    return $this->translationService->getTranslatedName($offer);
  }
}
