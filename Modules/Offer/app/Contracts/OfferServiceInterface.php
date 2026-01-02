<?php

namespace Modules\Offer\Contracts;

use Illuminate\Support\Collection;
use Modules\Offer\Entities\Offer\Offer;
use Illuminate\Contracts\Database\Eloquent\Builder;

interface OfferServiceInterface
{
    /**
     * Get offers by their IDs.
     *
     * @param array $offerIds Array of offer IDs
     * @return Collection Collection of offers
     */
    public function getOffersByIds(array $offerIds);

    /**
     * Get offers count.
     *
     * @return int Count of offers
     */
    public function getOffersCount(): int;

    /**
     * Get offers query builder.
     *
     * @return Builder Query builder instance
     */
    public function getQueryBuilder(): Builder;

    /**
     * Get offers products count.
     *
     * @param Offer $offer Offer instance
     * @return int Count of products
     */
    public function getProductsCount(Offer $offer): int;

    /**
     * Get translated name of offer.
     *
     * @param Offer $offer Offer instance
     * @return string Translated name
     */
    public function getTranslatedName(Offer $offer): string;

    /**
     * Get discount amount of offer.
     *
     * @param Offer $offer Offer instance
     * @return string Discount amount
     */
    public function getDiscountAmount(Offer $offer): string;
}
