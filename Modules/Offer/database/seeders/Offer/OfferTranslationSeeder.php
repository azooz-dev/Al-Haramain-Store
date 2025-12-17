<?php

namespace Modules\Offer\Database\Seeders\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Illuminate\Database\Seeder;
use Modules\Offer\Entities\Offer\OfferTranslation;
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
