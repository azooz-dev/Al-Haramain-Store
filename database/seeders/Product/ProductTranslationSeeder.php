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
        // Get all products that don't have translations
        $productsWithoutTranslations = Product::whereDoesntHave('translations')->get();

        // Create translations for products that don't have them
        foreach ($productsWithoutTranslations as $product) {
            // Create English translation
            ProductTranslation::create([
                'product_id' => $product->id,
                'local' => 'en',
                'name' => 'Product ' . $product->id,
                'description' => 'Description for product ' . $product->id,
            ]);

            // Create Arabic translation
            ProductTranslation::create([
                'product_id' => $product->id,
                'local' => 'ar',
                'name' => 'منتج ' . $product->id,
                'description' => 'وصف المنتج ' . $product->id,
            ]);
        }

        // Create some additional random translations for variety
        ProductTranslation::factory(50)->create();

        // Create specific product translations for testing (only if they don't exist)
        $firstProduct = Product::first();
        if ($firstProduct) {
            // Check if English translation exists
            if (!$firstProduct->translations()->where('local', 'en')->exists()) {
                ProductTranslation::create([
                    'product_id' => $firstProduct->id,
                    'name' => 'Product 1',
                    'description' => 'Product 1 description',
                    'local' => 'en',
                ]);
            }

            // Check if Arabic translation exists
            if (!$firstProduct->translations()->where('local', 'ar')->exists()) {
                ProductTranslation::create([
                    'product_id' => $firstProduct->id,
                    'name' => 'منتج رقم 1',
                    'description' => 'وصف المنتج رقم 1',
                    'local' => 'ar',
                ]);
            }
        }

        $this->command->info('Created translations for ' . $productsWithoutTranslations->count() . ' products.');
    }
}
