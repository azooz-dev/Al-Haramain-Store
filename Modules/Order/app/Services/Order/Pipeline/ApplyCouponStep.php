<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\Coupon\Services\Coupon\CouponService;
use Modules\Catalog\Services\Product\Variant\ProductVariantService;

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
            // Get the coupon to retrieve its ID
            $coupon = $this->couponService->applyCoupon($data['coupon_code'], (int)$data['user_id']);

            $totalAmount = $this->couponService->applyCouponToOrder(
                $data['coupon_code'],
                $totalAmount,
                (int)$data['user_id']
            );

            // Store the coupon ID for the order
            $data['coupon_id'] = $coupon->id;
        }

        $data['total_amount'] = $totalAmount;

        return $next($data);
    }
}
