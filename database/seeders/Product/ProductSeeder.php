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
        // Create sample products using factory
        Product::factory(100)->create()->each(function ($product) {
            // Attach random categories to each product
            $categoryIds = Category::inRandomOrder()->limit(fake()->numberBetween(1, 3))->pluck('id');
            $product->categories()->attach($categoryIds);
        });

        // Create specific product for testing
        $product = Product::create([
            'slug' => 'product-1',
            'sku' => '1234567890',
            'quantity' => 10,
        ]);

        // Attach random categories (assuming you want to attach, for example, 2 random categories)
        $categoryIds = Category::inRandomOrder()->limit(2)->pluck('id');
        $product->categories()->attach($categoryIds);
    }
}
