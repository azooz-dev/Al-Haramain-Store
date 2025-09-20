<?php

namespace App\Rules;

use Closure;
use App\Services\Offer\OfferService;
use App\Services\Product\ProductService;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOrderItem implements ValidationRule
{
    public function __construct(
        private ProductService $productService,
        private OfferService $offerService,
        private array $item
    ) {
        $this->productService = $productService;
        $this->offerService = $offerService;
        $this->item = $item;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->item['orderable_type'] === 'product') {
            $productId = $this->item['orderable_id'];
            $colorId = $this->item['color_id'] ?? null;
            $variantId = $this->item['variant_id'] ?? null;

            if ($productId && !$this->productService->getProductById($productId)) {
                $fail(__('app.messages.order.validation.product_not_found'));
            }

            if ($colorId && !$this->productService->checkColorBelongsToProduct($productId, $colorId)) {
                $fail(__('app.messages.order.validation.color_not_found'));
            }

            if ($variantId && !$this->productService->checkVariantBelongsToProductAndColor($productId, $colorId, $variantId)) {
                $fail(__('app.messages.order.validation.variant_not_found'));
            }

            return;
        } elseif ($this->item['orderable_type'] === 'offer') {
            $offerId = $this->item['orderable_id'];

            if ($this->offerService->retrieveOfferById($offerId)) {
                $fail(__("app.messages.order.validation.offer_not_found"));
            }

            return;
        }
    }
}
