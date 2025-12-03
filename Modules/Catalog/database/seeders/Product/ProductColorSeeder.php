<?php

namespace Modules\Catalog\Database\Seeders\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample product colors using factory
        ProductColor::factory(150)->create();

        // Create specific product color for testing
        ProductColor::create([
            'product_id' => Product::all()->random()->id,
            'color_code' => '#FF5733'
        ]);
    }
}

