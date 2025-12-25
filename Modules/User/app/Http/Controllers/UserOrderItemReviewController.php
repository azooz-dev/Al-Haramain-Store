<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Entities\User;
use function App\Helpers\showOne;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Modules\Order\Entities\Order\Order;
use Modules\Review\Entities\Review\Review;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Review\Http\Requests\Review\ReviewRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\User\Contracts\UserOrderItemReviewServiceInterface;

class UserOrderItemReviewController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserOrderItemReviewServiceInterface $reviewService) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request, User $user, Order $order, OrderItem $item)
    {
        // Check if authenticated user matches the user in the route
        if (Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Check if order belongs to the user
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $data = $request->validated();
        $review = $this->reviewService->storeAllOrderReviews($data, $user->id, $order->id, $item->id);
        return showOne($review, 'review', 201);
    }
}
