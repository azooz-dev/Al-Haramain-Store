<?php

namespace Modules\Offer\Contracts;

use Modules\Offer\Entities\Offer\Offer;

interface OfferTranslationServiceInterface
{
    /**
     * Get translated name for an offer
     *
     * @param Offer $offer
     * @param string|null $locale
     * @return string
     */
    public function getTranslatedName(Offer $offer, ?string $locale = null): string;

    /**
     * Get form data for an offer
     *
     * @param Offer $offer
     * @return array
     */
    public function getFormData(Offer $offer): array;

    /**
     * Save translation data for an offer
     *
     * @param Offer $offer
     * @param array $translationData
     * @return void
     */
    public function saveTranslation(Offer $offer, array $translationData): void;
}

