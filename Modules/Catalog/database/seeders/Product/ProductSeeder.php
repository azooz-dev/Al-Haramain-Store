<?php

namespace Modules\Catalog\Database\Seeders\Product;

use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'id' => 1,
                'slug' => 'aged-heat-treated-natural-amber-dhrwi-tesbih-52g-45-beads-13x9mm-1',
                'sku' => 'AMB',
                'quantity' => 100,
                'deleted_at' => null,
                'created_at' => '2025-09-17 15:34:56',
                'updated_at' => '2025-09-17 16:31:50',
            ],
            [
                'id' => 2,
                'slug' => 'luxury-soft-amber-prayer-beads',
                'sku' => 'NWM',
                'quantity' => 50,
                'deleted_at' => null,
                'created_at' => '2025-09-17 16:41:49',
                'updated_at' => '2025-09-17 16:41:49',
            ],
            [
                'id' => 3,
                'slug' => 'luxury-dark-sky-blue-faturan-prayer-beads',
                'sku' => 'FAT-DSB-LUX',
                'quantity' => 10,
                'deleted_at' => null,
                'created_at' => '2025-09-17 17:07:33',
                'updated_at' => '2025-09-17 17:07:33',
            ],
            [
                'id' => 5,
                'slug' => 'black-faturan-tasbih-trabzon',
                'sku' => 'FAT-BLK-TRB-12',
                'quantity' => 3,
                'deleted_at' => null,
                'created_at' => '2025-09-17 17:17:17',
                'updated_at' => '2025-09-17 17:17:17',
            ],
            [
                'id' => 6,
                'slug' => 'kuk-wood-prayer-beads-12mm',
                'sku' => 'WO33TABR316',
                'quantity' => 40,
                'deleted_at' => null,
                'created_at' => '2025-09-17 17:43:12',
                'updated_at' => '2025-09-17 17:43:12',
            ],
            [
                'id' => 7,
                'slug' => 'ebony-barrel-prayer-beads',
                'sku' => 'EB-BRL-001',
                'quantity' => 14,
                'deleted_at' => null,
                'created_at' => '2025-09-17 17:47:41',
                'updated_at' => '2025-09-17 17:47:41',
            ],
            [
                'id' => 8,
                'slug' => 'dark-maroon-bakelite-prayer-beads',
                'sku' => 'BAK-MAR-001',
                'quantity' => 20,
                'deleted_at' => null,
                'created_at' => '2025-09-17 17:51:36',
                'updated_at' => '2025-09-17 17:51:36',
            ],
            [
                'id' => 9,
                'slug' => 'mint-bakelite-prayer-beads',
                'sku' => 'BAK-MNT-001',
                'quantity' => 30,
                'deleted_at' => null,
                'created_at' => '2025-09-17 17:56:11',
                'updated_at' => '2025-09-17 17:56:11',
            ],
            [
                'id' => 10,
                'slug' => 'chameleon-bakelite-prayer-beads',
                'sku' => 'BAK-CHM-001',
                'quantity' => 4,
                'deleted_at' => null,
                'created_at' => '2025-09-17 17:59:56',
                'updated_at' => '2025-09-17 17:59:56',
            ],
            [
                'id' => 11,
                'slug' => 'velvet-quran-with-embroidered-name',
                'sku' => 'QUR-EMB-NAM-001',
                'quantity' => 50,
                'deleted_at' => null,
                'created_at' => '2025-09-17 18:33:15',
                'updated_at' => '2025-09-17 18:33:15',
            ],
            [
                'id' => 12,
                'slug' => 'royal-silk-prayer-rug-3-million-knots-futoon-red',
                'sku' => 'PRG-SILK-3M-FUT-RED',
                'quantity' => 25,
                'deleted_at' => null,
                'created_at' => '2025-09-17 18:40:43',
                'updated_at' => '2025-09-17 18:40:43',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['id' => $product['id']],
                $product
            );
        }
    }
}
