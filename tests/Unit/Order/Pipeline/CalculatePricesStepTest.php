<?php

namespace Tests\Unit\Order\Pipeline;

use Tests\TestCase;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use App\Models\Offer\Offer;
use App\Services\Order\Pipeline\CalculatePricesStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

/**
 * Unit tests for CalculatePricesStep
 */
class CalculatePricesStepTest extends TestCase
{
    use RefreshDatabase;

    private CalculatePricesStep $step;

    protected function setUp(): void
    {
        parent::setUp();
        $this->step = app(CalculatePricesStep::class);
    }

    /**
     * Test price calculation for product variant
     */
    public function test_price_calculation_for_product_variant(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 100,
            'price' => 100.00,
            'amount_discount_price' => 80.00, // Effective price
        ]);

        $variants = Collection::make([$variant->id => $variant]);

        $data = [
            '_variants' => $variants,
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant->id,
                    'quantity' => 2,
                ],
            ],
        ];

        // Act
        $result = $this->step->handle($data, fn($data) => $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result['items']);
        $this->assertEquals(160.00, $result['items'][0]['total_price']); // 80 * 2
    }

    /**
     * Test price calculation for offer
     */
    public function test_price_calculation_for_offer(): void
    {
        // Arrange
        $offer = Offer::factory()->active()->create(['offer_price' => 150.00]);
        $offers = Collection::make([$offer->id => $offer]);

        $data = [
            '_offers' => $offers,
            'items' => [
                [
                    'orderable_type' => Offer::class,
                    'orderable_id' => $offer->id,
                    'quantity' => 3,
                ],
            ],
        ];

        // Act
        $result = $this->step->handle($data, fn($data) => $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result['items']);
        $this->assertEquals(450.00, $result['items'][0]['total_price']); // 150 * 3
    }

    /**
     * Test price calculation for mixed items
     */
    public function test_price_calculation_for_mixed_items(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 100,
            'price' => 100.00,
        ]);

        $offer = Offer::factory()->active()->create(['offer_price' => 200.00]);

        $variants = Collection::make([$variant->id => $variant]);
        $offers = Collection::make([$offer->id => $offer]);

        $data = [
            '_variants' => $variants,
            '_offers' => $offers,
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant->id,
                    'quantity' => 2,
                ],
                [
                    'orderable_type' => Offer::class,
                    'orderable_id' => $offer->id,
                    'quantity' => 1,
                ],
            ],
        ];

        // Act
        $result = $this->step->handle($data, fn($data) => $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result['items']);
        $this->assertEquals(200.00, $result['items'][0]['total_price']); // 100 * 2
        $this->assertEquals(200.00, $result['items'][1]['total_price']); // 200 * 1
    }
}
