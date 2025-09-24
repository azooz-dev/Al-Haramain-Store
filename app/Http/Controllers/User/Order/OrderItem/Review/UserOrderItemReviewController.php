<?php

namespace App\Http\Controllers\User\Order\OrderItem\Review;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;

use function App\Helpers\showOne;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewRequest;
use App\Services\User\Order\OrderItem\Review\UserOrderItemReviewService;

class UserOrderItemReviewController extends Controller
{
    public function __construct(private UserOrderItemReviewService $reviewService) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request, User $user, Order $order, OrderItem $item)
    {
        $data = $request->validated();
        $review = $this->reviewService->storeAllOrderReviews($data, $user->id, $order->id, $item->id);
        return showOne($review, 'review', 201);
    }
}
