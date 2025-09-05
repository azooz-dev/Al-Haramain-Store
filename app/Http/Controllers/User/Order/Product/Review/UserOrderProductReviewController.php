<?php

namespace App\Http\Controllers\User\Order\Product\Review;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Product\Product;

use function App\Helpers\showOne;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewRequest;
use App\Services\User\Order\Product\Review\UserOrderProductReviewService;

class UserOrderProductReviewController extends Controller
{
    public function __construct(private UserOrderProductReviewService $reviewService) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request, User $user, Order $order, Product $product)
    {
        $data = $request->validated();
        $review = $this->reviewService->storeAllOrderReviews($data, $user->id, $order->id, $product->id);
        return showOne($review, 'review', 201);
    }
}
