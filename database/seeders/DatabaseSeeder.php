<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Category\CategorySeeder;
use Database\Seeders\Category\CategoryTranslationSeeder;
use Database\Seeders\Product\ProductColorImageSeeder;
use Database\Seeders\Product\ProductColorSeeder;
use Database\Seeders\Product\ProductSeeder;
use Database\Seeders\Product\ProductVariantSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            CategorySeeder::class,
            CategoryTranslationSeeder::class,
            ProductSeeder::class,
            ProductColorSeeder::class,
            ProductVariantSeeder::class,
            ProductColorImageSeeder::class
        ]);
    }
}
