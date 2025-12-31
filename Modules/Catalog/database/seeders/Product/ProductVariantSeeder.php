<?php

namespace Modules\Catalog\Database\Seeders\Product;

use Modules\Catalog\Entities\Product\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            ['id' => 1, 'product_id' => 1, 'color_id' => 1, 'size' => '33', 'price' => '199.00', 'amount_discount_price' => null, 'quantity' => 40, 'created_at' => '2025-09-17 15:34:56', 'updated_at' => '2025-09-17 15:34:56'],
            ['id' => 2, 'product_id' => 1, 'color_id' => 1, 'size' => '66', 'price' => '199.00', 'amount_discount_price' => null, 'quantity' => 30, 'created_at' => '2025-09-17 15:34:56', 'updated_at' => '2025-09-17 15:34:56'],
            ['id' => 3, 'product_id' => 1, 'color_id' => 1, 'size' => '99', 'price' => '199.00', 'amount_discount_price' => null, 'quantity' => 30, 'created_at' => '2025-09-17 15:34:56', 'updated_at' => '2025-09-17 15:34:56'],
            ['id' => 4, 'product_id' => 2, 'color_id' => 2, 'size' => '66', 'price' => '359.00', 'amount_discount_price' => '300.00', 'quantity' => 20, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 5, 'product_id' => 2, 'color_id' => 2, 'size' => '99', 'price' => '499.00', 'amount_discount_price' => '300.00', 'quantity' => 30, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 6, 'product_id' => 3, 'color_id' => 3, 'size' => '33', 'price' => '799.00', 'amount_discount_price' => '690.00', 'quantity' => 7, 'created_at' => '2025-09-17 17:07:33', 'updated_at' => '2025-09-17 17:07:33'],
            ['id' => 7, 'product_id' => 3, 'color_id' => 3, 'size' => '66', 'price' => '799.00', 'amount_discount_price' => '690.00', 'quantity' => 3, 'created_at' => '2025-09-17 17:07:33', 'updated_at' => '2025-09-17 17:07:33'],
            ['id' => 8, 'product_id' => 5, 'color_id' => 5, 'size' => '33', 'price' => '599.00', 'amount_discount_price' => null, 'quantity' => 3, 'created_at' => '2025-09-17 17:17:17', 'updated_at' => '2025-09-17 17:17:17'],
            ['id' => 9, 'product_id' => 6, 'color_id' => 6, 'size' => '33', 'price' => '120.00', 'amount_discount_price' => '79.00', 'quantity' => 40, 'created_at' => '2025-09-17 17:43:12', 'updated_at' => '2025-09-17 17:43:12'],
            ['id' => 10, 'product_id' => 7, 'color_id' => 7, 'size' => '33', 'price' => '75.00', 'amount_discount_price' => null, 'quantity' => 14, 'created_at' => '2025-09-17 17:47:41', 'updated_at' => '2025-09-17 17:47:41'],
            ['id' => 11, 'product_id' => 8, 'color_id' => 8, 'size' => '33', 'price' => '153.00', 'amount_discount_price' => null, 'quantity' => 20, 'created_at' => '2025-09-17 17:51:37', 'updated_at' => '2025-09-17 17:51:37'],
            ['id' => 12, 'product_id' => 9, 'color_id' => 9, 'size' => '33', 'price' => '179.00', 'amount_discount_price' => null, 'quantity' => 30, 'created_at' => '2025-09-17 17:56:11', 'updated_at' => '2025-09-17 17:56:11'],
            ['id' => 13, 'product_id' => 10, 'color_id' => 10, 'size' => '33', 'price' => '350.00', 'amount_discount_price' => null, 'quantity' => 4, 'created_at' => '2025-09-17 17:59:56', 'updated_at' => '2025-09-17 17:59:56'],
            ['id' => 14, 'product_id' => 11, 'color_id' => 11, 'size' => 'L', 'price' => '45.00', 'amount_discount_price' => null, 'quantity' => 50, 'created_at' => '2025-09-17 18:33:15', 'updated_at' => '2025-09-17 18:33:15'],
            ['id' => 15, 'product_id' => 12, 'color_id' => 12, 'size' => 'L', 'price' => '250.00', 'amount_discount_price' => null, 'quantity' => 25, 'created_at' => '2025-09-17 18:40:43', 'updated_at' => '2025-09-17 18:40:43'],
        ];

        foreach ($variants as $variant) {
            ProductVariant::updateOrCreate(
                ['id' => $variant['id']],
                $variant
            );
        }
    }
}
