<?php

namespace Modules\Offer\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Offer\Database\Seeders\Offer\OfferSeeder;
use Modules\Offer\Database\Seeders\Offer\OfferTranslationSeeder;
use Modules\Offer\Database\Seeders\Offer\OfferProductSeeder;

class OfferDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            OfferSeeder::class,
            OfferTranslationSeeder::class,
            OfferProductSeeder::class, // Must run after offers and products are seeded
        ]);
    }
}
