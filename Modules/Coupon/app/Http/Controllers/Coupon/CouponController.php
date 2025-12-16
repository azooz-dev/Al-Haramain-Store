<?php

namespace Modules\Coupon\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Modules\Coupon\Services\Coupon\CouponService;

use function App\Helpers\showOne;

class CouponController extends Controller
{
    public function __construct(private CouponService $couponService) {}
    /**
     * Display a listing of the resource.
     */
    public function apply(string $couponCode, int $userId)
    {
        $couponValidationResponse = $this->couponService->applyCoupon($couponCode, $userId);

        return showOne($couponValidationResponse, 'coupon', 200);
    }
}
