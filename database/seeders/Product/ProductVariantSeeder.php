<?php

namespace Database\Seeders\Product;

use App\Models\Product\ProductVariant;
use App\Models\Product\Product;
use App\Models\Product\ProductColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
