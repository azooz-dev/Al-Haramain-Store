<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Entities\User;
use Modules\Order\Entities\Order\Order;
use Modules\Review\Entities\Review\Review;

use Modules\Order\Entities\OrderItem\OrderItem;
use function App\Helpers\showOne;
use App\Http\Controllers\Controller;
use Modules\Review\Http\Requests\Review\ReviewRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\User\Services\UserOrderItemReviewService;

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
