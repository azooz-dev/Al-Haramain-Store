<?php

namespace App\Services\Offer;

use App\Http\Resources\Offer\OfferApiResource;
use App\Models\Offer\Offer;
use App\Repositories\Interface\Offer\OfferTranslationRepositoryInterface;
use Filament\Notifications\Collection;

class OfferTranslationService
{
  public function __construct(private OfferTranslationRepositoryInterface $translationRepository) {}

  public function getTranslationsForOffer(Offer $offer): Collection
  {
    return $this->translationRepository->getTranslationsForOffer($offer);
  }

  public function getTranslatedName(Offer $offer, ?string $locale = null): string
  {
    $locale = $locale ?: app()->getLocale();
    $translation = $this->translationRepository->getTranslationByLocale($offer, $locale);

    if (!$translation) {
      $translation = $this->translationRepository->getTranslationByLocale($offer, 'en');
    }

    return $translation->name ?? "";
  }

  public function saveTranslation(Offer $offer, array $translationData): void
  {
    foreach (['en', 'ar'] as $locale) {
      $payload = $translationData[$locale] ?? [];

      if (!empty($payload['name'] || !empty($payload['description']))) {
        $this->translationRepository->updateOrCreateTranslation($offer, $locale, $payload);
      }
    }
  }

  public function getFormData(Offer $offer): array
  {
    return (new OfferApiResource($offer->load('translations')))->toArray(request());
  }
}
