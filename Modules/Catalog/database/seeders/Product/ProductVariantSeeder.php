<?php

namespace Modules\Catalog\Database\Seeders\Product;

use Modules\Catalog\Entities\Product\ProductVariant;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample product variants using factory
        ProductVariant::factory(250)->create();

        // Create specific product variant for testing
        ProductVariant::create([
            'product_id' => Product::all()->random()->id,
            'color_id' => ProductColor::all()->random()->id,
            'size' => 'M',
            'price' => 100,
            'amount_discount_price' => 90,
            'quantity' => 10,
        ]);
    }
}

