<?php

namespace Modules\Offer\Database\Seeders\Offer;

use Modules\Offer\Entities\Offer\OfferProduct;
use Illuminate\Database\Seeder;

class OfferProductSeeder extends Seeder
{
    public function run(): void
    {
        $offerProducts = [
            ['id' => 2, 'offer_id' => 1, 'product_id' => 12, 'product_variant_id' => 15, 'product_color_id' => 12, 'variant_price' => '250.00', 'quantity' => 1, 'created_at' => '2025-09-17 23:11:15', 'updated_at' => '2025-09-17 23:11:15'],
            ['id' => 3, 'offer_id' => 1, 'product_id' => 8, 'product_variant_id' => 11, 'product_color_id' => 8, 'variant_price' => '153.00', 'quantity' => 1, 'created_at' => '2025-09-18 12:02:12', 'updated_at' => '2025-09-18 12:02:12'],
            ['id' => 6, 'offer_id' => 3, 'product_id' => 11, 'product_variant_id' => 14, 'product_color_id' => 11, 'variant_price' => '45.00', 'quantity' => 1, 'created_at' => '2025-09-18 13:54:38', 'updated_at' => '2025-09-18 13:54:38'],
            ['id' => 7, 'offer_id' => 3, 'product_id' => 9, 'product_variant_id' => 12, 'product_color_id' => 9, 'variant_price' => '179.00', 'quantity' => 1, 'created_at' => '2025-09-18 13:54:38', 'updated_at' => '2025-09-18 13:54:38'],
            ['id' => 8, 'offer_id' => 4, 'product_id' => 8, 'product_variant_id' => 11, 'product_color_id' => 8, 'variant_price' => '153.00', 'quantity' => 1, 'created_at' => '2025-09-18 14:46:17', 'updated_at' => '2025-09-18 14:46:17'],
            ['id' => 9, 'offer_id' => 4, 'product_id' => 11, 'product_variant_id' => 14, 'product_color_id' => 11, 'variant_price' => '45.00', 'quantity' => 1, 'created_at' => '2025-09-18 14:46:17', 'updated_at' => '2025-09-18 14:46:17'],
            ['id' => 10, 'offer_id' => 4, 'product_id' => 12, 'product_variant_id' => 15, 'product_color_id' => 12, 'variant_price' => '250.00', 'quantity' => 1, 'created_at' => '2025-09-18 14:46:17', 'updated_at' => '2025-09-18 14:46:17'],
        ];

        foreach ($offerProducts as $offerProduct) {
            OfferProduct::updateOrCreate(
                ['id' => $offerProduct['id']],
                $offerProduct
            );
        }
    }
}
