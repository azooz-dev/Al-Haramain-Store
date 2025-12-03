<?php

namespace Tests\Support\Builders;

use App\Models\User\User;
use App\Models\Order\Order;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use App\Models\Coupon\Coupon;
use App\Models\Offer\Offer;
use App\Models\User\UserAddresses\Address;

/**
 * Builder Pattern for creating test data
 */
class OrderTestDataBuilder
{
    private ?User $user = null;
    private ?Address $address = null;
    private array $products = [];
    private array $offers = [];
    private ?Coupon $coupon = null;
    private string $paymentMethod = Order::PAYMENT_METHOD_CASH_ON_DELIVERY;
    private bool $userVerified = true;

    public static function create(): self
    {
        return new self();
    }

    public function withVerifiedUser(array $attributes = []): self
    {
        $this->user = User::factory()
            ->verified()
            ->create($attributes);
        $this->userVerified = true;
        return $this;
    }

    public function withUnverifiedUser(array $attributes = []): self
    {
        $this->user = User::factory()
            ->unverified()
            ->create($attributes);
        $this->userVerified = false;
        return $this;
    }

    public function withUserAddress(array $attributes = []): self
    {
        if (!$this->user) {
            throw new \RuntimeException('User must be created before address');
        }

        $this->address = Address::factory()
            ->create(array_merge(['user_id' => $this->user->id], $attributes));
        return $this;
    }

    public function withProduct(array $productAttributes = [], array $variantAttributes = []): self
    {
        $product = Product::factory()->create($productAttributes);
        $color = ProductColor::factory()->create(['product_id' => $product->id]);

        $variant = ProductVariant::factory()
            ->create(array_merge([
                'product_id' => $product->id,
                'color_id' => $color->id,
                'quantity' => 100,
                'price' => 100.00,
                'amount_discount_price' => null,
            ], $variantAttributes));

        $this->products[] = [
            'product' => $product,
            'color' => $color,
            'variant' => $variant,
        ];

        return $this;
    }

    public function withOffer(array $offerAttributes = []): self
    {
        $offer = Offer::factory()
            ->active()
            ->create($offerAttributes);

        $this->offers[] = $offer;
        return $this;
    }

    public function withActiveCoupon(array $attributes = []): self
    {
        $this->coupon = Coupon::factory()
            ->active()
            ->create($attributes);
        return $this;
    }

    public function withCoupon(Coupon $coupon): self
    {
        $this->coupon = $coupon;
        return $this;
    }

    public function withPaymentMethod(string $method): self
    {
        $this->paymentMethod = $method;
        return $this;
    }

    public function buildOrderData(): array
    {
        if (!$this->user) {
            throw new \RuntimeException('User is required');
        }

        if (!$this->address) {
            $this->withUserAddress();
        }

        $items = [];

        // Add product items
        foreach ($this->products as $productData) {
            $items[] = [
                'orderable_type' => Product::class,
                'orderable_id' => $productData['product']->id,
                'color_id' => $productData['color']->id,
                'variant_id' => $productData['variant']->id,
                'quantity' => 1,
            ];
        }

        // Add offer items
        foreach ($this->offers as $offer) {
            $items[] = [
                'orderable_type' => Offer::class,
                'orderable_id' => $offer->id,
                'quantity' => 1,
                'variant_id' => null,
                'color_id' => null,
            ];
        }

        return [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'payment_method' => $this->paymentMethod,
            'coupon_code' => $this->coupon?->code,
            'items' => $items,
        ];
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }
}
