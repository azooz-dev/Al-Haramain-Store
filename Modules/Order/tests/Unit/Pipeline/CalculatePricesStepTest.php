<?php

namespace Modules\Order\tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\CalculatePricesStep;
use Modules\Catalog\Entities\Product\Product;
use Modules\Offer\Entities\Offer\Offer;

/**
 * TC-ORD-007: Order Creation - Multiple Items (Products + Offers)
 */
class CalculatePricesStepTest extends TestCase
{
    private CalculatePricesStep $step;

    protected function setUp(): void
    {
        parent::setUp();
        $this->step = new CalculatePricesStep();
    }

    public function test_calculates_prices_for_product_items(): void
    {
        // Arrange
        $variants = collect([
            1 => (object)['id' => 1, 'effective_price' => 100.00],
        ]);

        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => 1,
                    'quantity' => 2,
                ],
            ],
            '_variants' => $variants,
        ];

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertEquals(200.00, $result['items'][0]['total_price']);
    }

    public function test_calculates_prices_for_offer_items(): void
    {
        // Arrange
        $offers = collect([
            1 => (object)['id' => 1, 'offer_price' => 50.00],
        ]);

        $data = [
            'items' => [
                [
                    'orderable_type' => Offer::class,
                    'orderable_id' => 1,
                    'quantity' => 3,
                ],
            ],
            '_offers' => $offers,
        ];

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertEquals(150.00, $result['items'][0]['total_price']);
    }

    public function test_calculates_prices_for_mixed_items(): void
    {
        // Arrange
        $variants = collect([
            1 => (object)['id' => 1, 'effective_price' => 100.00],
        ]);

        $offers = collect([
            1 => (object)['id' => 1, 'offer_price' => 50.00],
        ]);

        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => 1,
                    'quantity' => 2,
                ],
                [
                    'orderable_type' => Offer::class,
                    'orderable_id' => 1,
                    'quantity' => 1,
                ],
            ],
            '_variants' => $variants,
            '_offers' => $offers,
        ];

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertEquals(200.00, $result['items'][0]['total_price']);
        $this->assertEquals(50.00, $result['items'][1]['total_price']);
    }
}

