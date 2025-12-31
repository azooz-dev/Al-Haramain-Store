<?php

namespace Modules\Catalog\Database\Seeders\Product;

use Modules\Catalog\Entities\Product\ProductColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productColors = [
            [
                'id' => 1,
                'product_id' => 1,
                'color_code' => '#c07020',
                'created_at' => '2025-09-17 15:34:56',
                'updated_at' => '2025-09-17 15:34:56',
            ],
            [
                'id' => 2,
                'product_id' => 2,
                'color_code' => '#fceb8b',
                'created_at' => '2025-09-17 16:41:49',
                'updated_at' => '2025-09-17 16:41:49',
            ],
            [
                'id' => 3,
                'product_id' => 3,
                'color_code' => '#1f3092',
                'created_at' => '2025-09-17 17:07:33',
                'updated_at' => '2025-09-17 17:07:33',
            ],
            [
                'id' => 5,
                'product_id' => 5,
                'color_code' => '#0a0a0c',
                'created_at' => '2025-09-17 17:17:17',
                'updated_at' => '2025-09-17 17:17:17',
            ],
            [
                'id' => 6,
                'product_id' => 6,
                'color_code' => '#cd8133',
                'created_at' => '2025-09-17 17:43:12',
                'updated_at' => '2025-09-17 17:43:12',
            ],
            [
                'id' => 7,
                'product_id' => 7,
                'color_code' => '#000000',
                'created_at' => '2025-09-17 17:47:41',
                'updated_at' => '2025-09-17 17:47:41',
            ],
            [
                'id' => 8,
                'product_id' => 8,
                'color_code' => '#7d0e1a',
                'created_at' => '2025-09-17 17:51:36',
                'updated_at' => '2025-09-17 17:51:36',
            ],
            [
                'id' => 9,
                'product_id' => 9,
                'color_code' => '#d4f0e5',
                'created_at' => '2025-09-17 17:56:11',
                'updated_at' => '2025-09-17 17:56:11',
            ],
            [
                'id' => 10,
                'product_id' => 10,
                'color_code' => '#492793',
                'created_at' => '2025-09-17 17:59:56',
                'updated_at' => '2025-09-17 17:59:56',
            ],
            [
                'id' => 11,
                'product_id' => 11,
                'color_code' => '#827e7e',
                'created_at' => '2025-09-17 18:33:15',
                'updated_at' => '2025-09-17 18:33:15',
            ],
            [
                'id' => 12,
                'product_id' => 12,
                'color_code' => '#1e0d0e',
                'created_at' => '2025-09-17 18:40:43',
                'updated_at' => '2025-09-17 18:40:43',
            ],
        ];

        foreach ($productColors as $productColor) {
            ProductColor::updateOrCreate(
                ['id' => $productColor['id']],
                $productColor
            );
        }
    }
}
