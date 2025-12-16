<?php

namespace Modules\Review\Repositories\Interface\Review;

interface ReviewRepositoryInterface extends 
    ReadReviewRepositoryInterface, 
    WriteReviewRepositoryInterface, 
    QueryableReviewRepositoryInterface
{
}
