<?php

namespace Modules\Offer\Contracts;

use Illuminate\Support\Collection;

interface OfferServiceInterface
{
    /**
     * Get offers by their IDs.
     *
     * @param array $offerIds Array of offer IDs
     * @return Collection Collection of offers
     */
    public function getOffersByIds(array $offerIds);
}

