<?php

namespace Modules\Catalog\Database\Seeders\Category;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryProducts = [
            ['id' => 1, 'category_id' => 1, 'product_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'category_id' => 1, 'product_id' => 2, 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'category_id' => 3, 'product_id' => 3, 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'category_id' => 3, 'product_id' => 5, 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'category_id' => 4, 'product_id' => 6, 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'category_id' => 4, 'product_id' => 7, 'created_at' => null, 'updated_at' => null],
            ['id' => 7, 'category_id' => 2, 'product_id' => 8, 'created_at' => null, 'updated_at' => null],
            ['id' => 8, 'category_id' => 2, 'product_id' => 9, 'created_at' => null, 'updated_at' => null],
            ['id' => 9, 'category_id' => 2, 'product_id' => 10, 'created_at' => null, 'updated_at' => null],
            ['id' => 10, 'category_id' => 6, 'product_id' => 11, 'created_at' => null, 'updated_at' => null],
            ['id' => 11, 'category_id' => 5, 'product_id' => 12, 'created_at' => null, 'updated_at' => null],
        ];

        foreach ($categoryProducts as $categoryProduct) {
            DB::table('category_product')->updateOrInsert(
                ['id' => $categoryProduct['id']],
                $categoryProduct
            );
        }
    }
}
