<?php

namespace Database\Seeders\Offer;

use App\Models\Offer\Offer;
use Illuminate\Database\Seeder;
use App\Models\Offer\OfferTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfferTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample offer translations using factory
        OfferTranslation::factory(50)->create();

        // Create specific offer translations for testing
        OfferTranslation::create([
            'name' => 'Offer 1',
            'description' => 'This is a test offer',
            'locale' => 'en',
            'offer_id' => Offer::first()->id
        ]);

        OfferTranslation::create([
            'name' => 'العرض 1',
            'description' => 'وصف العرض الاول',
            'locale' => 'ar',
            'offer_id' => Offer::first()->id
        ]);
    }
}
