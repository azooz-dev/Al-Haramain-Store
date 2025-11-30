<?php


namespace App\Repositories\Eloquent\Offer;

use App\Models\Offer\Offer;
use App\Models\Offer\OfferProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interface\Offer\OfferRepositoryInterface;

class OfferRepository implements OfferRepositoryInterface
{
  public function getAllOffers(): Collection
  {
    return Offer::with(['translations', 'products'])->where('status', Offer::ACTIVE)->get();
  }

  public function findOfferById(int $offerId): ?Offer
  {
    return Offer::with(['translations', 'products'])->find($offerId);
  }

  public function getOfferProducts(int $offerId): Collection
  {
    return OfferProduct::where("offer_id", $offerId)->get();
  }

  public function findOffersByIds(array $offerIds): Collection
  {
    return Offer::whereIn('id', $offerIds)->with('offerProducts')->get();
  }

  public function create(array $data): Offer
  {
    return Offer::create($data);
  }

  public function update(int $id, array $data): Offer
  {
    $offer = Offer::findOrFail($id);
    $offer->update($data);
    return $offer->fresh([
      'translations',
      'offerProducts.product.translations',
      'offerProducts.productVariant',
      'offerProducts.productColor',
    ]);
  }

  public function delete(int $id): bool
  {
    $offer = Offer::findOrFail($id);
    return $offer->delete();
  }

  public function count(): int
  {
    return Offer::count();
  }

  public function getQueryBuilder(): Builder
  {
    return Offer::query()
      ->with([
        'translations',
        'offerProducts.product.translations',
        'offerProducts.productVariant',
        'offerProducts.productColor',
      ])
      ->withCount(['offerProducts']);
  }
}
