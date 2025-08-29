<?php

namespace App\Repositories\Eloquent\Offer;

use App\Models\Offer\Offer;
use App\Models\Offer\OfferTranslation;
use Filament\Notifications\Collection;
use App\Repositories\interface\Offer\OfferTranslationRepositoryInterface;

class OfferTranslationRepository implements OfferTranslationRepositoryInterface
{
  public function getTranslationsForOffer(Offer $offer): Collection
  {
    return $offer->translations;
  }

  public function getTranslationByLocale(Offer $offer, string $locale): ?OfferTranslation
  {
    return $offer->translations()->where('locale', $locale)->first();
  }

  public function saveTranslation(Offer $offer, string $locale, array $data): OfferTranslation
  {
    return OfferTranslation::create([
      'name' => $data['name'],
      'description' => $data['description'],
      'locale' => $locale
    ]);
  }

  public function updateOrCreateTranslation(Offer $offer, string $locale, array $data): OfferTranslation
  {
    return OfferTranslation::updateOrCreate(
      [
        'offer_id' => $offer->id,
        'locale' => $locale,
      ],
      [
        'name' => $data['name'] ?? '',
        'description' => $data['description'] ?? '',
      ]
    );
  }

  public function deleteTranslationsForOffer(Offer $offer): bool
  {
    return $offer->translations()->delete();
  }

  public function searchTranslations(string $search, string $failed = 'name'): Collection
  {
    return OfferTranslation::where($failed, 'like', "%{$search}%")->get();
  }
}
