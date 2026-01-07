<?php

namespace Modules\Offer\Database\Seeders\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'id' => 1,
                'image_path' => 'offers/images/01K6K5TDDACPZQ1DFPMD6JSE6F.jpg',
                'products_total_price' => '403.00',
                'offer_price' => '200.00',
                'start_date' => '2025-12-19',
                'end_date' => '2025-12-30',
                'status' => 'active',
                'created_at' => '2025-12-17 23:11:15',
                'updated_at' => '2025-12-18 12:02:12',
            ],
            [
                'id' => 3,
                'image_path' => 'offers/images/01K6K5W5MG0WZ2ZS6D5W8PPS2N.jpg',
                'products_total_price' => '224.00',
                'offer_price' => '120.00',
                'start_date' => '2025-12-19',
                'end_date' => '2025-12-30',
                'status' => 'active',
                'created_at' => '2025-12-18 13:54:38',
                'updated_at' => '2025-12-18 13:55:54',
            ],
            [
                'id' => 4,
                'image_path' => 'offers/images/01K6K5YWRHJQG64EWQ92VMZGYW.png',
                'products_total_price' => '448.00',
                'offer_price' => '159.00',
                'start_date' => '2025-12-19',
                'end_date' => '2025-12-30',
                'status' => 'active',
                'created_at' => '2025-12-18 14:46:17',
                'updated_at' => '2025-12-18 15:12:37',
            ],
        ];

        foreach ($offers as $offer) {
            Offer::updateOrCreate(
                ['id' => $offer['id']],
                $offer
            );
        }
    }
}
