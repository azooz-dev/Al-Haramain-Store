<?php

namespace Modules\Offer\Database\Seeders\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Modules\Offer\Enums\OfferStatus;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure products exist before attaching offers
        if (Product::count() === 0) {
            Product::factory(20)->create();
        }

        // Create sample offers
        Offer::factory(25)->create()->each(function (Offer $offer) {
            $productIds = Product::inRandomOrder()->limit(rand(1, 5))->pluck('id');
            $offer->products()->attach($productIds);
        });

        // Create specific offer for testing
        $offer = Offer::create([
            'image_path' => 'https://images.unsplash.com/photo-1607748862156-7c548e7e98f4?w=800&h=600&fit=crop',
            'products_total_price' => 100,
            'offer_price' => 100,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => OfferStatus::ACTIVE,
        ]);
        $offer->products()->attach(Product::inRandomOrder()->limit(3)->pluck('id'));
    }
}
