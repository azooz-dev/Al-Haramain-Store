<?php

namespace Database\Seeders\Offer;

use App\Models\Offer\OfferProduct;
use App\Models\Offer\Offer;
use App\Models\Product\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OfferProduct::create([
            'offer_id' => Offer::random()->id,
            'product_id' => Product::random()->id,
        ]);
    }
}
