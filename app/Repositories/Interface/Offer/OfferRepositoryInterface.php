<?php

namespace App\Repositories\Interface\Offer;

interface OfferRepositoryInterface extends 
    ReadOfferRepositoryInterface, 
    WriteOfferRepositoryInterface, 
    QueryableOfferRepositoryInterface
{
}
