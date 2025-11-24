<?php

namespace App\Services\Order\Pipeline;

use App\Services\Coupon\CouponService;
use App\Services\Product\Variant\ProductVariantService;

class ApplyCouponStep implements OrderProcessingStep
{
    public function __construct(
        private CouponService $couponService,
        private ProductVariantService $variantService
    ) {}

    public function handle(array $data, \Closure $next)
    {
        // Calculate total
        $totalAmount = $this->variantService->calculateTotalOrderPrice($data['items']);

        // Apply coupon if provided
        if (!empty($data['coupon_code'])) {
            $totalAmount = $this->couponService->applyCouponToOrder(
                $data['coupon_code'], 
                $totalAmount, 
                (int)$data['user_id']
            );
        }

        $data['total_amount'] = $totalAmount;

        return $next($data);
    }
}


