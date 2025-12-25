<?php

namespace Modules\Review\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Review\Contracts\ReviewServiceInterface;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Http\Resources\Review\ReviewApiResource;
use function App\Helpers\showAll;
use function App\Helpers\showOne;

class ReviewController extends Controller
{
    public function __construct(private ReviewServiceInterface $reviewService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = $this->reviewService->getQueryBuilder()
            ->where('status', 'approved')
            ->get();
        
        return showAll(ReviewApiResource::collection($reviews), 'Reviews', 200);
    }

    /**
     * Show the specified resource.
     */
    public function show(int $id)
    {
        $review = $this->reviewService->getReviewById($id);
        return showOne($review, 'Review', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_map(fn($s) => $s->value, ReviewStatus::cases())),
        ]);

        $review = $this->reviewService->updateReviewStatus($id, $data['status']);
        return showOne($review, 'Review', 200);
    }
}
