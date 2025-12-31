<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Database\Seeders\Category\CategorySeeder;
use Modules\Catalog\Database\Seeders\Category\CategoryTranslationSeeder;
use Modules\Catalog\Database\Seeders\Category\CategoryProductSeeder;
use Modules\Catalog\Database\Seeders\Product\ProductSeeder;
use Modules\Catalog\Database\Seeders\Product\ProductColorSeeder;
use Modules\Catalog\Database\Seeders\Product\ProductColorImageSeeder;
use Modules\Catalog\Database\Seeders\Product\ProductTranslationSeeder;
use Modules\Catalog\Database\Seeders\Product\ProductVariantSeeder;

class CatalogDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            CategoryTranslationSeeder::class,
            ProductSeeder::class,
            ProductColorSeeder::class,
            ProductVariantSeeder::class,
            ProductColorImageSeeder::class,
            ProductTranslationSeeder::class,
            CategoryProductSeeder::class, // Must run after categories and products are seeded
        ]);
    }
}
