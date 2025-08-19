<?php

namespace Database\Seeders\Product;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Seeder;

class ProductTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductTranslation::create([
            'product_id' => Product::first()->id,
            'name' => 'Product 1',
            'description' => 'Product 1 description',
            'local' => 'en',
        ]);

        ProductTranslation::create([
            'product_id' => Product::first()->id,
            'name' => 'منتج رقم 1',
            'description' => 'وصف المنتج رقم 1',
            'local' => 'ar',
        ]);
    }
}
