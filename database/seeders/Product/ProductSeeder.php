<?php

namespace Database\Seeders\Product;

use App\Models\Category\Category;
use App\Models\Product\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Product 1',
            'slug' => 'product-1',
            'sku' => '1234567890',
            'description' => 'Product 1 description',
            'price' => 100,
            'amount_discount_price' => 90,
            'quantity' => 10,
            'categories' => Category::all()->random()->pluck('id'),
        ]);
    }
}
