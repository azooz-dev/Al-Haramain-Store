<?php

namespace Database\Seeders\Offer;

use App\Models\Offer\OfferCategory;
use App\Models\Offer\Offer;
use App\Models\Category\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OfferCategory::create([
            'offer_id' => Offer::random()->id,
            'category_id' => Category::random()->id,
        ]);
    }
}
