<?php

namespace Modules\Offer\Repositories\Interface\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Modules\Offer\Entities\Offer\OfferTranslation;
use Filament\Notifications\Collection;

interface OfferTranslationRepositoryInterface
{
  public function getTranslationsForOffer(Offer $offer): Collection;

  public function getTranslationByLocale(Offer $offer, string $locale): ?OfferTranslation;

  public function saveTranslation(Offer $offer, string $locale, array $data): OfferTranslation;

  public function updateOrCreateTranslation(Offer $offer, string $locale, array $data): OfferTranslation;

  public function deleteTranslationsForOffer(Offer $offer): bool;

  public function searchTranslations(string $search, string $failed = 'name'): Collection;
}
