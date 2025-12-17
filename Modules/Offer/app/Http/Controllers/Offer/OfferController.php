<?php

namespace Modules\Offer\Http\Controllers\Offer;

use App\Http\Controllers\Controller;
use Modules\Offer\Services\Offer\OfferService;

use function App\Helpers\showAll;
use function App\Helpers\showOne;

class OfferController extends Controller
{
    public function __construct(private OfferService $offerService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = $this->offerService->fetchAllOffers();

        return showAll($offers, 'Offers', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $offerId)
    {
        $offer = $this->offerService->findOfferById($offerId);

        return showOne($offer, 'Offer', 200);
    }
}
