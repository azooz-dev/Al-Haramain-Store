<?php

namespace App\Repositories\Interface\Review;

interface ReviewRepositoryInterface extends 
    ReadReviewRepositoryInterface, 
    WriteReviewRepositoryInterface, 
    QueryableReviewRepositoryInterface
{
}
