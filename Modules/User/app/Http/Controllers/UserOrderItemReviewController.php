<?php

namespace Modules\User\Http\Controllers;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Review\Review;

use App\Models\Order\OrderItem;
use function App\Helpers\showOne;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\User\Order\OrderItem\Review\UserOrderItemReviewService;

class UserOrderItemReviewController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserOrderItemReviewService $reviewService) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request, User $user, Order $order, OrderItem $item)
    {
        $this->authorize('create', [Review::class, $user]);

        $data = $request->validated();
        $review = $this->reviewService->storeAllOrderReviews($data, $user->id, $order->id, $item->id);
        return showOne($review, 'review', 201);
    }
}
