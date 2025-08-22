<?php

namespace Database\Seeders\Product;

use App\Models\Product\ProductColor;
use App\Models\Product\ProductColorImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductColorImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductColorImage::create([
            'product_color_id' => ProductColor::all()->random()->id,
            'image_url' => 'image.jpg',
        ]);
    }
}
