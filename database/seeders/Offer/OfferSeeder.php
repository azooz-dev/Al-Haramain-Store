<?php

namespace Database\Seeders\Offer;

use App\Models\Offer\Offer;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample offers using factory
        Offer::factory(25)->create();

        // Create specific offer for testing
        Offer::create([
            'image_path' => 'https://images.unsplash.com/photo-1607748862156-7c548e7e98f4?w=800&h=600&fit=crop',
            'discount_type' => Offer::FIXED,
            'discount_amount' => 100,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'product_id' => Product::first()->id,
            'status' => Offer::ACTIVE,
        ]);
    }
}
