<?php

namespace App\Rules;

use Closure;
use App\Models\Offer\Offer;
use App\Models\Product\Product;
use App\Services\Offer\OfferService;
use App\Services\Product\ProductService;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOrderItem implements ValidationRule
{
    public function __construct(
        private ProductService $productService,
        private OfferService $offerService,
        private array $items
    ) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->items as $item) {

            if ($item['orderable_type'] === Product::class) {
                $productId = $item['orderable_id'];
                $colorId = $item['color_id'] ?? null;
                $variantId = $item['variant_id'] ?? null;

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
            } elseif ($item['orderable_type'] === Offer::class) {
                $offerId = $item['orderable_id'];

                if (!$this->offerService->retrieveOfferById($offerId)) {
                    $fail(__("app.messages.order.validation.offer_not_found"));
                }

                return;
            }
        }
    }
}
